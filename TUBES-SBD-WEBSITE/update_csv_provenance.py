#!/usr/bin/env python3
"""
UPDATE EXISTING CSV - Fill empty provenance columns
Scrapes provenance for rows with empty/NaN values and updates CSV in-place
"""

import os
import csv
import time
import logging
import pandas as pd
from pathlib import Path
from typing import Optional, Tuple

import warnings
warnings.filterwarnings('ignore')

from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.chrome.service import Service

logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)

CSV_PATH = r'C:\Users\gidio\OneDrive\document\TUBES-SBD-K3-METMUSEUM\TUBES-SBD-WEBSITE\database\data\metmuseum_provenance_final.csv'

stats = {"total": 0, "updated": 0, "skipped": 0, "empty": 0, "errors": 0}


def parse_provenance_text(raw_text: str) -> str:
    """
    Parse provenance text from MetMuseum website with CORRECT rules:
    
    ATURAN PENTING:
    - Split HANYA by newline (\n) atau <br> tags
    - JANGAN split by semicolon (;) - itu bagian text normal
    - JANGAN split by comma (,) - itu bagian text normal
    - JANGAN split by tanda baca lain
    
    INPUT:  "Thomas Henry Foley 4th Baron Foley ; Henry Thomas Foley 5th Baron Foley\n
             Fitzalan Charles John Foley 6th Baron Foley\n
             Patricia Meek Dowager Lady Foley"
    
    OUTPUT: "Thomas Henry Foley 4th Baron Foley ; Henry Thomas Foley 5th Baron Foley
             Fitzalan Charles John Foley 6th Baron Foley
             Patricia Meek Dowager Lady Foley"
    
    Jika tidak ada newline, return sebagai text utuh.
    """
    if not raw_text or not isinstance(raw_text, str):
        return ""
    
    # Split hanya by newline/line break
    lines = raw_text.splitlines()
    
    # Clean setiap line (strip whitespace), tapi PRESERVE content
    entries = [
        line.strip()
        for line in lines
        if line.strip()  # Filter empty lines only
    ]
    
    # Rejoin dengan newline (preserve original structure)
    cleaned_text = '\n'.join(entries)
    
    return cleaned_text.strip()


def setup_browser():
    """Setup Chrome WebDriver"""
    options = Options()
    options.add_argument('--headless')
    options.add_argument('--no-sandbox')
    options.add_argument('--disable-dev-shm-usage')
    options.add_argument('user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36')
    
    # Disable images for faster loading
    options.add_experimental_option(
        'prefs',
        {'profile.managed_default_content_settings.images': 2}
    )
    
    service = Service(ChromeDriverManager().install())
    driver = webdriver.Chrome(service=service, options=options)
    driver.set_page_load_timeout(45)
    return driver


def extract_provenance(driver, url: str, object_id: str) -> Optional[str]:
    """
    Extract provenance from Met Museum page
    Uses proven approach: click tab + extract from bodyWrapper
    """
    if not url.startswith('http'):
        url = 'http://' + url
    
    try:
        driver.get(url)
        time.sleep(1.5)
        
        # Wait for artwork-details to load
        WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.ID, "artwork-details"))
        )
        
        # Click Provenance tab if available
        try:
            prov_buttons = driver.find_elements(
                By.XPATH,
                "//button[contains(., 'Provenance')] | //div[contains(@class, 'tabText') and contains(., 'Provenance')]"
            )
            if prov_buttons:
                prov_buttons[0].click()
                time.sleep(1)
        except Exception as e:
            logger.debug(f"[{object_id}] Tab click attempted: {str(e)[:40]}")
        
        # Extract from bodyWrapper divs
        try:
            body_wrappers = driver.find_elements(By.XPATH, "//div[contains(@class, 'bodyWrapper')]")
            
            if body_wrappers:
                for wrapper in body_wrappers:
                    text = wrapper.text
                    if text and len(text.strip()) > 20:
                        # Parse provenance dengan rules: split by newline ONLY, preserve semicolons
                        provenance = parse_provenance_text(text)
                        
                        # Filter out common section headers
                        lines = provenance.split('\n')
                        filtered_lines = []
                        for line in lines:
                            if line in ['Exhibition History', 'References', 'Publications']:
                                break
                            filtered_lines.append(line)
                        
                        final_prov = '\n'.join(filtered_lines).strip()
                        
                        if final_prov and len(final_prov) > 20 and 'Exhibition History' not in final_prov:
                            logger.info(f"✓ [{object_id}] FOUND: {len(final_prov)} chars")
                            stats['updated'] += 1
                            return final_prov
        
        except Exception as e:
            logger.debug(f"[{object_id}] Extraction error: {str(e)[:40]}")
        
        logger.warning(f"⚠ [{object_id}] Empty provenance")
        stats['empty'] += 1
        return None
        
    except Exception as e:
        logger.error(f"✗ [{object_id}] Error: {str(e)[:80]}")
        stats['errors'] += 1
        return None


def main():
    # Load existing CSV
    logger.info(f"Loading existing CSV...")
    df = pd.read_csv(CSV_PATH, encoding='utf-8')
    original_rows = len(df)
    logger.info(f"Loaded {original_rows} rows from CSV")
    
    # Identify empty provenance rows
    empty_mask = df['provenance'].isna() | (df['provenance'] == '')
    empty_count = empty_mask.sum()
    logger.info(f"Rows with empty provenance: {empty_count}")
    logger.info(f"Rows with existing provenance: {original_rows - empty_count}")
    
    if empty_count == 0:
        logger.info("✓ All rows have provenance. Nothing to update!")
        return
    
    # Setup browser
    driver = setup_browser()
    
    try:
        row_index = 0
        for idx, row in df.iterrows():
            row_index += 1
            
            # Only process empty provenance
            if pd.notna(df.loc[idx, 'provenance']) and df.loc[idx, 'provenance'] != '':
                stats['skipped'] += 1
                continue
            
            met_object_id = str(row['met_object_id']).strip()
            link_resource = str(row['link_resource']).strip()
            
            # Validate data
            if not met_object_id or met_object_id == 'nan' or not link_resource or link_resource == 'nan':
                stats['errors'] += 1
                continue
            
            logger.info(f"\n[{row_index}/{original_rows}] Updating: {met_object_id}")
            
            provenance = extract_provenance(driver, link_resource, met_object_id)
            
            # Update CSV in memory
            if provenance:
                df.loc[idx, 'provenance'] = provenance
            else:
                df.loc[idx, 'provenance'] = ''
            
            stats['total'] += 1
            
            # Progress report every 50 rows
            if row_index % 50 == 0:
                logger.info(f"\n========== PROGRESS ==========")
                logger.info(f"Processed: {row_index}/{original_rows}")
                logger.info(f"Updated: {stats['updated']}")
                logger.info(f"Empty: {stats['empty']}")
                logger.info(f"Errors: {stats['errors']}")
                logger.info(f"Skipped (already filled): {stats['skipped']}")
                logger.info(f"=============================\n")
            
            time.sleep(0.3)
    
    finally:
        driver.quit()
    
    # Save updated CSV (overwrite original)
    logger.info(f"\nSaving updated CSV to: {CSV_PATH}")
    df.to_csv(CSV_PATH, index=False, encoding='utf-8', quoting=csv.QUOTE_MINIMAL)
    
    # Validation
    logger.info(f"\n{'='*80}")
    logger.info(f"VALIDATION CHECK")
    logger.info(f"{'='*80}")
    
    df_verify = pd.read_csv(CSV_PATH, encoding='utf-8')
    logger.info(f"✓ Rows preserved: {len(df_verify)} == {original_rows}: {len(df_verify) == original_rows}")
    logger.info(f"✓ Columns preserved: {list(df_verify.columns)} == ['met_object_id', 'link_resource', 'provenance']: {list(df_verify.columns) == ['met_object_id', 'link_resource', 'provenance']}")
    logger.info(f"✓ No duplicates: {df_verify.duplicated(subset=['met_object_id']).sum() == 0}")
    logger.info(f"✓ File size: {os.path.getsize(CSV_PATH) / 1024:.1f} KB")
    
    print("\n" + "="*80)
    print("                    CSV UPDATE COMPLETE")
    print("="*80)
    print(f"\n[SUMMARY]")
    print(f"  Total rows: {original_rows}")
    print(f"  Total processed: {stats['total']}")
    print(f"  Successfully updated: {stats['updated']} ({stats['updated']/max(stats['total'],1)*100:.1f}%)")
    print(f"  Empty (not found): {stats['empty']}")
    print(f"  Errors: {stats['errors']}")
    print(f"  Skipped (already filled): {stats['skipped']}")
    print(f"\n[FILE]")
    print(f"  Path: {CSV_PATH}")
    print(f"  Status: ✓ Updated and saved")
    print("="*80 + "\n")


if __name__ == '__main__':
    main()
