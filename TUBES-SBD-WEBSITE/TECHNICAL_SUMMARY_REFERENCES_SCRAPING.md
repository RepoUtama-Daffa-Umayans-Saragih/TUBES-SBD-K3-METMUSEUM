================================================================================
     RANGKUMAN TEKNIS SCRAPING REFERENCES - METMUSEUM DATABASE PROJECT
================================================================================

Dibuat: 2026-05-15
Project: TUBES-SBD-K3 MetMuseum Website
Status: Pre-Implementation Technical Analysis


================================================================================
1. KONTEKS & TUJUAN SCRAPING
================================================================================

TARGET DATA:
  ✓ Section "References" / "Bibliography" dari halaman artwork MetMuseum
  ✓ Data diambil per artwork berdasarkan list curated CSV
  ✓ Tujuan: Memperkaya database art_works dengan metadata references

MENGAPA DIPERLUKAN:
  • Provenance (already scraped) = informasi kepemilikan historis artwork
  • References/Bibliography = sumber literatur, publikasi, dokumentasi artwork
  • Dua data ini melengkapi informasi artwork di database museum
  • References nantinya untuk keperluan research, citation, dan dokumentasi

SKALA PROJECT:
  • 2000 artwork records dari curated CSV
  • Direncanakan scraping untuk setiap artwork
  • Data akan diintegrasikan ke MySQL database (Laravel)


================================================================================
2. DATA SOURCES - ALUR DATA DARI SUMBER KE OUTPUT
================================================================================

A. INPUT DATA SOURCE
   └─ CSV Curated (Source of Truth)
      ├─ File: metmuseum_curated_full_columns_2000.csv
      ├─ Location: database/data/
      ├─ Rows: 2000 artwork records
      ├─ Columns Used:
      │   ├─ Object ID / met_object_id (primary key untuk linking)
      │   └─ Link Resource / link_resource (URL halaman artwork)
      └─ Columns NOT Used:
          ├─ Title, Date, Medium (untuk references scraping tidak relevan)
          └─ dll


B. PROSES SCRAPING & TRANSFORMATION
   
   Input CSV
   ├─ met_object_id: 503046
   ├─ link_resource: http://www.metmuseum.org/art/collection/search/503046
   │
   ├─ [STEP 1] Open halaman di Selenium
   ├─ [STEP 2] Click tab "References" (jika ada)
   ├─ [STEP 3] Extract text dari section References
   │   │
   │   ├─ Raw text dari website:
   │   │   "Christie's, London (sale 1919)
   │   │    Metropolitan Museum of Fine Arts (acquired 1950)
   │   │    Published in: Smith, J. (1975) Grand Pianos of Europe"
   │   │
   │   └─ [STEP 4] Parse & Split by line/paragraph
   │       └─ Hasilkan multiple references untuk 1 artwork
   │
   └─ Output CSV (multi-row per artwork)


C. OUTPUT DATA STRUCTURE
   
   Output CSV: metmuseum_references_final.csv
   
   Row 1 (Header):
   met_object_id | link_resource | references
   
   Row 2-N (Data):
   503046 | http://www.metmuseum.org/art/collection/search/503046 | Reference A
   503046 | http://www.metmuseum.org/art/collection/search/503046 | Reference B
   503046 | http://www.metmuseum.org/art/collection/search/503046 | Reference C
   503530 | http://www.metmuseum.org/art/collection/search/503530 | Reference D
   
   PENTING: Setiap row = 1 reference (bukan gabungan)


================================================================================
3. STRUKTUR DATA DALAM DATABASE
================================================================================

A. EXISTING TABLE: art_works

   Column            Type        Description
   ────────────────  ──────────  ───────────────────────────────────────
   art_work_id       INT (PK)    Auto-increment primary key
   met_object_id     INT         Unique identifier dari MetMuseum
   title             VARCHAR     Judul artwork
   link_resource     VARCHAR     URL halaman MetMuseum
   provenance        LONGTEXT    Informasi kepemilikan historis (sudah scraped)
   description       LONGTEXT    Deskripsi artwork
   date              VARCHAR     Tanggal dibuat artwork
   medium            VARCHAR     Media/bahan artwork
   dimensions        VARCHAR     Ukuran artwork
   created_at        TIMESTAMP   Waktu record dibuat
   updated_at        TIMESTAMP   Waktu record diupdate


B. NEW TABLE: art_work_references (untuk references)

   Column                 Type        Description
   ──────────────────────  ──────────  ───────────────────────────────────────
   art_work_reference_id  INT (PK)    Auto-increment primary key
   art_work_id            INT (FK)    Foreign key ke art_works.art_work_id
   reference_text         LONGTEXT    Text reference/bibliography
   display_order          INT         Urutan display (1, 2, 3, ...)
   source_type            VARCHAR     Jenis sumber (book, article, museum, dll)
   created_at             TIMESTAMP   Waktu record dibuat
   updated_at             TIMESTAMP   Waktu record diupdate


C. RELASI DATABASE

   Relasi: 1 to Many

   art_works (1)
   ├─ art_work_id = 1
   ├─ met_object_id = 503046
   ├─ title = "Grand Pianoforte"
   └─ link_resource = "http://www.metmuseum.org/art/collection/search/503046"
      │
      └─ art_work_references (Many)
         ├─ art_work_reference_id = 100
         ├─ art_work_id = 1  (FK)
         ├─ reference_text = "Christie's, London (sale May 27, 1919, lot 115)"
         ├─ display_order = 1
         │
         ├─ art_work_reference_id = 101
         ├─ art_work_id = 1  (FK)
         ├─ reference_text = "Hirschl and Adler Galleries, New York, until 1987"
         ├─ display_order = 2
         │
         └─ art_work_reference_id = 102
            ├─ art_work_id = 1  (FK)
            ├─ reference_text = "Metropolitan Museum of Fine Arts records"
            └─ display_order = 3


================================================================================
4. FLOW INTEGRASI CSV → DATABASE
================================================================================

PHASE 1: SCRAPING (Output CSV)

   ┌─────────────────────────────────────────────┐
   │ CSV Curated (Input)                         │
   │                                             │
   │ met_object_id | link_resource              │
   │ 503046        | http://www.metmuseum.org...│
   │ 503530        | http://www.metmuseum.org...│
   └────────────────┬────────────────────────────┘
                    │
                    ▼
   ┌─────────────────────────────────────────────┐
   │ Selenium Scraper                            │
   │ (Extract References Section)                │
   └────────────────┬────────────────────────────┘
                    │
                    ▼
   ┌─────────────────────────────────────────────┐
   │ CSV Output (Multi-row per artwork)          │
   │                                             │
   │ met_object_id | link_resource | references │
   │ 503046        | http://...     | Ref A      │
   │ 503046        | http://...     | Ref B      │
   │ 503046        | http://...     | Ref C      │
   │ 503530        | http://...     | Ref D      │
   └─────────────────────────────────────────────┘


PHASE 2: DATABASE INTEGRATION (Insert)

   Step 1: Baca CSV Output
   ─────────────────────────────────────────────

   ```
   foreach row in CSV:
       met_object_id = row['met_object_id']
       reference_text = row['references']
   ```


   Step 2: Lookup art_work_id dari met_object_id
   ─────────────────────────────────────────────

   SQL Query:
   ```sql
   SELECT art_work_id 
   FROM art_works 
   WHERE met_object_id = 503046
   ```

   Result: art_work_id = 1


   Step 3: Insert ke art_work_references
   ─────────────────────────────────────────────

   SQL Insert:
   ```sql
   INSERT INTO art_work_references 
   (art_work_id, reference_text, display_order, created_at, updated_at)
   VALUES 
   (1, 'Ref A', 1, NOW(), NOW()),
   (1, 'Ref B', 2, NOW(), NOW()),
   (1, 'Ref C', 3, NOW(), NOW())
   ```


   Step 4: Hasil di Database
   ─────────────────────────────────────────────

   art_works table:
   ┌────────────┬───────────────┬────────────┐
   │ art_work_id│ met_object_id │ title      │
   ├────────────┼───────────────┼────────────┤
   │ 1          │ 503046        │ Grand Piano│
   └────────────┴───────────────┴────────────┘

   art_work_references table:
   ┌──────────────────┬────────────┬──────────────────────┬───────────┐
   │ art_work_ref_id  │ art_work_id│ reference_text       │ order     │
   ├──────────────────┼────────────┼──────────────────────┼───────────┤
   │ 100              │ 1          │ Ref A                │ 1         │
   │ 101              │ 1          │ Ref B                │ 2         │
   │ 102              │ 1          │ Ref C                │ 3         │
   └──────────────────┴────────────┴──────────────────────┴───────────┘


================================================================================
5. STRATEGI PARSING REFERENCES DARI HALAMAN METMUSEUM
================================================================================

A. STRUKTUR HTML HALAMAN METMUSEUM

   Halaman artwork memiliki beberapa sections:

   ┌─ Artwork Details (Title, Date, Medium)
   ├─ Description
   ├─ Dimensions
   ├─ Provenance                    ← Sudah di-scrape sebelumnya
   ├─ Exhibition History
   ├─ References / Bibliography     ← TARGET SCRAPING SAAT INI
   └─ Inscription/Markings


B. TAB-BASED STRUCTURE (React Component)

   Halaman MetMuseum menggunakan React tabs:

   ┌────────────────────────────────────┐
   │ [Details] [Description] [Provenance] │
   │    [Exhibition] [References]        │  ← Ini adalah tabs
   └────────────────────────────────────┘

   PENTING:
   • References mungkin berada di tab terpisah (tidak auto-loaded)
   • Tab harus di-CLICK untuk menampilkan content
   • Content hanya render setelah click (React state management)


C. SELECTOR STRATEGY

   Langkah 1: Tunggu page load
   ────────────────────────────────
   WebDriverWait(driver, 10).until(
       EC.presence_of_element_located((By.ID, "artwork-details"))
   )


   Langkah 2: Cari dan click References tab
   ────────────────────────────────────────
   XPath candidates:
   • //div[contains(@class, 'tabText') and contains(., 'References')]
   • //button[contains(., 'References')]
   • //tab[contains(text(), 'References')]

   Click:
   reference_tab = driver.find_elements(
       By.XPATH, 
       "//div[contains(@class, 'tabText') and contains(., 'References')]"
   )[0]
   reference_tab.click()
   time.sleep(1.5)  # Wait for content render


   Langkah 3: Extract dari container div
   ──────────────────────────────────────
   XPath untuk content container:
   • //div[contains(@class, 'bodyWrapper')]   ← Proven selector (dari provenance)
   • //section[contains(@class, 'references')]
   • //div[contains(@class, 'references')]

   Ekstraksi:
   body_wrappers = driver.find_elements(
       By.XPATH, 
       "//div[contains(@class, 'bodyWrapper')]"
   )
   references_text = body_wrappers[0].text


D. TEXT PARSING & CLEANING

   Raw text dari website:
   ───────────────────────
   "Christie's, London (sale May 27, 1919, lot 115)
    
    Hirschl and Adler Galleries, New York, until 1987
    
    [Sotheby's, New York, 1995]
    
    Metropolitan Museum, 2000"


   Parsing Strategy:
   ─────────────────
   1. Split by newline (\n)
   2. Strip whitespace tiap line
   3. Filter empty lines
   4. Deteksi line breaks (paragraph separator)
   5. Group references per paragraph


   Hasil setelah parsing:
   ──────────────────────
   [
       "Christie's, London (sale May 27, 1919, lot 115)",
       "Hirschl and Adler Galleries, New York, until 1987",
       "[Sotheby's, New York, 1995]",
       "Metropolitan Museum, 2000"
   ]


   Final output (per row CSV):
   ──────────────────────────
   503046 | http://... | Christie's, London (sale May 27, 1919, lot 115)
   503046 | http://... | Hirschl and Adler Galleries, New York, until 1987
   503046 | http://... | [Sotheby's, New York, 1995]
   503046 | http://... | Metropolitan Museum, 2000


================================================================================
6. STRUKTUR CSV HASIL SCRAPING - DETAILED
================================================================================

A. FORMAT SPESIFIKASI

   File: metmuseum_references_final.csv
   Encoding: UTF-8
   Delimiter: Comma (,)
   Quote: Minimal (hanya jika ada comma dalam text)


B. HEADER ROW

   met_object_id,link_resource,references

   Kolom definitions:
   
   met_object_id
   ├─ Type: INTEGER
   ├─ Source: Dari CSV curated (tidak generate baru)
   ├─ Purpose: Link ke database art_works
   └─ Example: 503046


   link_resource
   ├─ Type: VARCHAR/URL
   ├─ Source: Dari CSV curated (preserve as-is)
   ├─ Purpose: Reference ke halaman MetMuseum (audit trail)
   └─ Example: http://www.metmuseum.org/art/collection/search/503046


   references
   ├─ Type: LONGTEXT
   ├─ Source: Scraped dari halaman artwork
   ├─ Purpose: Text bibliography/reference yang akan disimpan di database
   ├─ Content: Satu reference per row
   └─ Example: Christie's, London (sale May 27, 1919, lot 115)


C. DATA ROWS EXAMPLE

   Row 2-4 (503046 - Grand Pianoforte, 3 references):
   ──────────────────────────────────────────────────
   503046,http://www.metmuseum.org/art/collection/search/503046,"Christie's, London (sale May 27, 1919, lot 115)"
   503046,http://www.metmuseum.org/art/collection/search/503046,"Hirschl and Adler Galleries, New York, until 1987"
   503046,http://www.metmuseum.org/art/collection/search/503046,"Metropolitan Museum acquired 2000"

   Row 5-6 (503530 - Violin, 2 references):
   ──────────────────────────────────────────
   503530,http://www.metmuseum.org/art/collection/search/503530,"Sotheby's, New York (sale June 19, 2002)"
   503530,http://www.metmuseum.org/art/collection/search/503530,"Published: Musical Instruments of Europe (Smith, 1975)"


D. HANDLING SPECIAL CHARACTERS

   CSV Escaping Rules:
   ─────────────────

   Original text: Christie's, London (1919)
   CSV output: "Christie's, London (1919)"
   → Quote jika ada apostrophe atau comma

   Original text: Reference includes "quote" marks
   CSV output: "Reference includes ""quote"" marks"
   → Double quotes untuk escape

   UTF-8 encoding:
   ────────────────
   Support untuk: é, ñ, ü, 中文, etc.
   → Pandas to_csv dengan encoding='utf-8'


E. DATA VALIDATION

   Setiap row HARUS memiliki:
   ✓ met_object_id (tidak boleh null/empty)
   ✓ link_resource (tidak boleh null/empty)
   ✓ references (boleh empty jika tidak ditemukan)

   Validasi struktur:
   ✓ 3 kolom (tidak lebih, tidak kurang)
   ✓ Header row 1
   ✓ Data rows dimulai row 2
   ✓ Tidak ada duplicate full rows
   ✓ UTF-8 encoding
   ✓ CRLF atau LF line endings (konsisten)


================================================================================
7. PENANGANAN DATA KOSONG & ERROR HANDLING
================================================================================

A. SKENARIO REFERENCES TIDAK DITEMUKAN

   Skenario 1: References section tidak ada di halaman
   ──────────────────────────────────────────────────
   
   Action:
   • Skip row untuk artwork tersebut
   • Log sebagai "No references found"
   • Lanjut ke artwork berikutnya (JANGAN crash)

   Output CSV:
   • TIDAK ada row untuk artwork tersebut
   • Row CSV untuk artwork ini tidak ada


   Skenario 2: References section ada, tapi kosong
   ───────────────────────────────────────────────
   
   Action:
   • Insert 1 row dengan references = empty string ("")
   • Log sebagai "Empty references"
   • Lanjut ke artwork berikutnya

   Output CSV:
   503046,http://www.metmuseum.org/art/collection/search/503046,""


   Skenario 3: Page load timeout / network error
   ──────────────────────────────────────────────
   
   Action:
   • Catch exception
   • Log error dengan detail
   • Skip artwork ini
   • Lanjut ke artwork berikutnya

   Output CSV:
   • TIDAK ada row
   • Error logged untuk manual review


B. ERROR HANDLING STRATEGY

   Try-Except wrap untuk setiap artwork:

   ```python
   for each artwork in curated_csv:
       try:
           met_object_id = artwork['met_object_id']
           link = artwork['link_resource']
           
           # Open page
           driver.get(link)
           
           # Wait & click tab
           click_references_tab()
           
           # Extract references
           references = extract_references()
           
           # Parse & split by paragraph
           ref_list = parse_references(references)
           
           # Write to CSV (1 row per reference)
           for ref in ref_list:
               write_csv_row(met_object_id, link, ref)
           
       except ElementNotFound:
           log_warning(f"References tab not found for {met_object_id}")
           
       except TimeoutException:
           log_error(f"Page timeout for {met_object_id}")
           
       except Exception as e:
           log_error(f"Unexpected error for {met_object_id}: {e}")
       
       finally:
           # Always proceed to next artwork
           continue
   ```


================================================================================
8. MENJAGA URUTAN DATA & KONSISTENSI
================================================================================

A. ALASAN URUTAN PENTING

   Urutan data harus konsisten dengan CSV curated karena:

   1. Database Sync
      • saat import ke database, urutan membantu validasi
      • memudahkan cross-check dengan source CSV
      • jika ada gap, mudah terdeteksi

   2. Incremental Update
      • jika scraping gagal di tengah jalan
      • dapat di-resume dari row X tanpa duplicate
      • log progress berbasis row number

   3. Audit Trail
      • verifikasi: "scraping row 250-300 pada jam X"
      • jika ada inconsistency, mudah di-trace


B. IMPLEMENTASI URUTAN

   Cara preserve urutan:

   ✓ Baca CSV curated sequentially (tidak shuffle)
   ✓ Gunakan enumerate() untuk row index
   ✓ Tulis CSV output in order (tidak buffer & randomize)
   ✓ Gunakan file append mode atau buffer in memory lalu save once


   Implementasi:

   ```python
   # Load CSV curated (ordered)
   curated_df = pd.read_csv('metmuseum_curated_full_columns_2000.csv')
   
   # Process sequentially
   output_rows = []
   
   for idx, row in curated_df.iterrows():  # Sequential iteration
       met_object_id = row['Object ID']
       link = row['Link Resource']
       
       # Scrape
       references = scrape_references(link, met_object_id)
       
       # Parse & append (maintain order)
       for ref in references:
           output_rows.append({
               'met_object_id': met_object_id,
               'link_resource': link,
               'references': ref
           })
   
   # Write to CSV (ordered)
   output_df = pd.DataFrame(output_rows)
   output_df.to_csv(OUTPUT_CSV, index=False)
   ```


C. LOGGING PROGRESS

   Log untuk tracking:

   ```
   [1/2000] Processing: 503046
   [2/2000] Processing: 503530
   [3/2000] Processing: 470309
   ...
   [2000/2000] Processing: 35673
   
   Summary:
   - Total rows: 2000
   - References found: 1850
   - Empty/Not found: 150
   - Errors: 0
   - Output CSV rows: 5234 (average 2.6 references per artwork)
   ```


================================================================================
9. INTEGRASI HASIL SCRAPING KE DATABASE LARAVEL
================================================================================

A. WORKFLOW INTEGRASI

   Step 1: Upload CSV ke Laravel storage
   ──────────────────────────────────────
   ```
   database/data/metmuseum_references_final.csv
   ```


   Step 2: Create Laravel Migration untuk tabel art_work_references
   ────────────────────────────────────────────────────────────────
   ```php
   Schema::create('art_work_references', function (Blueprint $table) {
       $table->id('art_work_reference_id');
       $table->unsignedBigInteger('art_work_id');
       $table->longText('reference_text');
       $table->integer('display_order')->default(1);
       $table->string('source_type')->nullable();
       $table->timestamps();
       
       $table->foreign('art_work_id')
             ->references('art_work_id')
             ->on('art_works')
             ->onDelete('cascade');
   });
   ```


   Step 3: Create Laravel Command untuk import CSV
   ───────────────────────────────────────────────
   ```php
   php artisan make:command ImportArtworkReferences
   ```


   Step 4: Command Logic
   ─────────────────────
   ```php
   class ImportArtworkReferences extends Command {
       public function handle() {
           $csv_path = storage_path('app/database/data/metmuseum_references_final.csv');
           
           // Read CSV
           $rows = array_map('str_getcsv', file($csv_path));
           $header = array_shift($rows);  // Skip header
           
           foreach ($rows as $row) {
               $met_object_id = $row[0];
               $reference_text = $row[2];
               
               // Find art_work_id
               $artwork = ArtWork::where('met_object_id', $met_object_id)->first();
               
               if ($artwork && $reference_text) {
                   // Insert reference
                   ArtWorkReference::create([
                       'art_work_id' => $artwork->art_work_id,
                       'reference_text' => $reference_text,
                       'display_order' => get_next_order($artwork->art_work_id)
                   ]);
               }
           }
       }
   }
   ```


   Step 5: Run Command
   ──────────────────
   ```bash
   php artisan import:artwork-references
   ```


B. MODEL RELATIONSHIPS (Eloquent)

   ```php
   // app/Models/ArtWork.php
   class ArtWork extends Model {
       public $timestamps = true;
       protected $primaryKey = 'art_work_id';
       
       public function references() {
           return $this->hasMany(ArtWorkReference::class, 'art_work_id');
       }
   }
   
   
   // app/Models/ArtWorkReference.php
   class ArtWorkReference extends Model {
       public $timestamps = true;
       protected $primaryKey = 'art_work_reference_id';
       
       public function artwork() {
           return $this->belongsTo(ArtWork::class, 'art_work_id');
       }
   }
   ```


C. QUERY EXAMPLES

   Get all references for specific artwork:
   ```php
   $artwork = ArtWork::find(1);
   $references = $artwork->references()
       ->orderBy('display_order')
       ->get();
   ```


   Display references:
   ```php
   @forelse ($artwork->references as $ref)
       <p>{{ $ref->reference_text }}</p>
   @empty
       <p>No references found</p>
   @endforelse
   ```


================================================================================
10. PIPELINE SCRAPING PROJECT - COMPLETE ARCHITECTURE
================================================================================

A. COMPLETE DATA FLOW DIAGRAM

   ┌─────────────────────────────────────┐
   │ PHASE 1: DATA CURATION              │
   └────────────┬────────────────────────┘
                │
                ├─ Manual curation: Select 2000 artworks
                └─ Output: metmuseum_curated_full_columns_2000.csv
                   Columns: Object ID, Link Resource, Title, Date, Medium, etc.
                │
   ┌────────────▼────────────────────────┐
   │ PHASE 2: SCRAPE PROVENANCE          │
   └────────────┬────────────────────────┘
                │
                ├─ Script: scrape_provenance_production.py / update_csv_provenance.py
                ├─ Input: curated CSV
                ├─ Method: 
                │  • Click "Provenance" tab
                │  • Extract from bodyWrapper
                │  • Multi-row per artwork (if multiple provenance)
                └─ Output: metmuseum_provenance_final.csv
                   Columns: met_object_id, link_resource, provenance
                   Rows: ~2000-5000 (multi-row per artwork)
                │
   ┌────────────▼────────────────────────┐
   │ PHASE 3: SCRAPE REFERENCES          │
   └────────────┬────────────────────────┘
                │
                ├─ Script: scrape_references_production.py (TBD)
                ├─ Input: curated CSV
                ├─ Method:
                │  • Click "References" tab
                │  • Extract from references section
                │  • Parse by paragraph/line
                │  • Multi-row per artwork
                └─ Output: metmuseum_references_final.csv
                   Columns: met_object_id, link_resource, references
                   Rows: ~3000-8000 (multi-row per artwork)
                │
   ┌────────────▼────────────────────────┐
   │ PHASE 4: DATABASE IMPORT            │
   └────────────┬────────────────────────┘
                │
                ├─ Step 1: Import provenance
                │  • Read metmuseum_provenance_final.csv
                │  • Lookup met_object_id → art_work_id
                │  • Insert into art_works.provenance column
                │
                ├─ Step 2: Import references
                │  • Read metmuseum_references_final.csv
                │  • Lookup met_object_id → art_work_id
                │  • Insert into art_work_references table
                │  • Set display_order (1, 2, 3, ...)
                │
                └─ Laravel commands:
                   php artisan import:artwork-provenance
                   php artisan import:artwork-references
                │
   ┌────────────▼────────────────────────┐
   │ DATABASE FINAL STATE                │
   └─────────────────────────────────────┘
                │
                ├─ art_works (2000 rows)
                │  ├─ Columns: art_work_id, met_object_id, link_resource, 
                │  │            title, date, medium, provenance (from scrape),
                │  │            description, etc.
                │
                └─ art_work_references (~3000-8000 rows)
                   ├─ Columns: art_work_reference_id, art_work_id (FK),
                   │            reference_text (from scrape), display_order


B. FILE STRUCTURE PROJECT

   TUBES-SBD-WEBSITE/
   ├─ database/
   │  └─ data/
   │     ├─ metmuseum_curated_full_columns_2000.csv
   │     │  (SOURCE - tidak diubah, hanya dibaca)
   │     │
   │     ├─ metmuseum_provenance_final.csv
   │     │  (OUTPUT dari Phase 2)
   │     │
   │     └─ metmuseum_references_final.csv
   │        (OUTPUT dari Phase 3 - TBD)
   │
   ├─ app/
   │  ├─ Models/
   │  │  ├─ ArtWork.php
   │  │  └─ ArtWorkReference.php
   │  │
   │  └─ Console/
   │     └─ Commands/
   │        ├─ ImportArtworkProvenance.php
   │        └─ ImportArtworkReferences.php
   │
   ├─ database/
   │  ├─ migrations/
   │  │  ├─ ...create_art_works_table.php
   │  │  └─ ...create_art_work_references_table.php
   │  │
   │  └─ seeders/
   │     └─ ArtWorkSeeder.php
   │
   ├─ scripts/
   │  ├─ scrape_provenance_production.py
   │  ├─ update_csv_provenance.py
   │  ├─ scrape_references_production.py (TBD)
   │  └─ validate_scraped_data.py
   │
   └─ resources/
      └─ views/
         └─ art_works/
            └─ show.blade.php
               (Display provenance & references)


================================================================================
11. KEY INSIGHTS & IMPLEMENTATION STRATEGY
================================================================================

A. LESSONS LEARNED (dari scraping Provenance)

   1. React Tab Components
      ✓ Content tidak auto-load dengan page
      ✓ HARUS click tab untuk trigger rendering
      ✓ Perlu wait time setelah click (1.5 detik)

   2. Selector Stability
      ✓ Full CSS class names (generated oleh React) tidak reliable
      ✓ Gunakan partial match: contains(@class, 'bodyWrapper')
      ✓ XPath lebih robust dari CSS selectors untuk halaman dinamis

   3. Multi-row CSV untuk Related Data
      ✓ 1 artwork dapat memiliki multiple provenance entries
      ✓ Solution: 1 row CSV = 1 entry di tabel relasi
      ✓ BUKAN menggabungkan dengan separator

   4. Session Management
      ✓ WebDriver session dapat timeout/crash setelah 100-200 requests
      ✓ Perlu error handling & recovery
      ✓ Pertimbangkan: fresh WebDriver per 500 objects atau batch processing

   5. Data Validation
      ✓ Minimum length check (>20 chars) untuk filter noise
      ✓ Exclude section headers saat parsing
      ✓ Preserve original text (jangan over-sanitize)


B. REKOMENDASI IMPLEMENTASI SCRAPING REFERENCES

   Berdasarkan success provenance scraping, gunakan strategi:

   1. Sama dengan Provenance:
      ✓ Click tab "References"
      ✓ Extract dari bodyWrapper atau section container
      ✓ Wait 1.5 detik setelah click
      ✓ Error handling untuk skip artwork

   2. Berbeda dari Provenance:
      ✓ Parse result TIDAK menjadi 1 row
      ✓ Split by paragraph/line → multiple rows per artwork
      ✓ Setiap row = 1 reference entry
      ✓ Set display_order berdasarkan parse order

   3. Optimasi:
      ✓ Gunakan batch processing (500 objects per batch)
      ✓ Fresh WebDriver per batch untuk avoid session issues
      ✓ Implement progress checkpoint (resume dari row X jika fail)
      ✓ Comprehensive logging untuk debugging


================================================================================
12. SUMMARY & NEXT STEPS
================================================================================

A. PEMAHAMAN TEKNIS - CHECKLIST

   ✓ Tujuan scraping: Extract References/Bibliography dari MetMuseum
   ✓ Data source: Curated CSV dengan met_object_id & link_resource
   ✓ Target output: metmuseum_references_final.csv
   ✓ CSV structure: Multi-row per artwork (1 reference per row)
   ✓ Database integration: Insert to art_work_references table
   ✓ Relasi database: 1 artwork → many references
   ✓ Alur scraping: Open page → Click tab → Extract → Parse → CSV
   ✓ Error handling: Skip gracefully, continue to next artwork
   ✓ Data consistency: Preserve urutan, validate structure
   ✓ Pipeline integration: Phase dalam complete museum database project


B. READY FOR IMPLEMENTATION

   Segala logika, strategi, dan struktur data sudah mapped out.

   Next steps:
   1. Create: scrape_references_production.py
      (Based on proven scrape_provenance strategy)

   2. Run: Scrape all 2000 artworks untuk extract references

   3. Output: metmuseum_references_final.csv
      (Multi-row format untuk database import)

   4. Validate: Check structure, encoding, row count

   5. Import: Laravel command untuk insert ke art_work_references

   6. Verify: Query & display references untuk sample artworks


================================================================================
                             END OF TECHNICAL SUMMARY
================================================================================

Document Version: 1.0
Last Updated: 2026-05-15
Status: Ready for Implementation

Semua logika, struktur, dan strategi sudah dipahami sebelum coding dimulai.
Implementasi siap berdasarkan technical foundation ini.

================================================================================
