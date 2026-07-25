@extends('layouts.admin')
@section('title', 'Buat Laporan Penjualan')
@section('breadcrumb')
    <a href="{{ route('karyawan.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('karyawan.reports.index') }}">Laporan Saya</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Buat Laporan</span>
@endsection

@section('content')
<style>
    /* Wizard steps */
    .wizard-steps { display: flex; justify-content: space-between; margin-bottom: 30px; position: relative; }
    .wizard-steps::before {
        content: ''; position: absolute; top: 20px; left: 0; right: 0; height: 2px; background: var(--border-light); z-index: 1;
    }
    .wizard-step { position: relative; z-index: 2; display: flex; flex-direction: column; align-items: center; gap: 8px; flex: 1; text-align: center; color: var(--text-muted); transition: all var(--transition); }
    .wizard-step-circle {
        width: 40px; height: 40px; border-radius: var(--radius-full); background: var(--surface);
        border: 2px solid var(--border-light); display: flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: 16px; transition: all var(--transition);
    }
    .wizard-step.active { color: var(--primary); }
    .wizard-step.active .wizard-step-circle { background: var(--primary); border-color: var(--primary); color: #fff; box-shadow: 0 0 0 4px var(--primary-100); }
    .wizard-step.completed .wizard-step-circle { background: var(--success); border-color: var(--success); color: #fff; }
    
    .step-content { display: none; animation: fadeUpIn 0.4s ease forwards; }
    .step-content.active { display: block; }
    
    /* Product Selector */
    .product-search-container { position: relative; margin-bottom: 20px; }
    .product-dropdown {
        position: absolute; top: 100%; left: 0; right: 0; background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--radius-md); box-shadow: var(--shadow-md); z-index: 100; max-height: 250px; overflow-y: auto;
        display: none;
    }
    .product-dropdown.show { display: block; }
    .product-option { padding: 12px 16px; cursor: pointer; display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-light); }
    .product-option:hover { background: var(--primary-50); }
    .product-option:last-child { border-bottom: none; }
    
    .selected-product-card {
        background: var(--surface-secondary); border: 1px solid var(--border-light); border-radius: var(--radius-md);
        padding: 16px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;
        transition: all var(--transition-fast);
    }
    .selected-product-card:hover { border-color: var(--primary-300); }
    .qty-control { display: flex; align-items: center; gap: 8px; }
    .qty-btn { width: 32px; height: 32px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; }
    .qty-btn:hover { background: var(--primary-50); color: var(--primary); border-color: var(--primary-200); }
    .qty-input { width: 50px; text-align: center; border: 1px solid var(--border); border-radius: var(--radius-sm); height: 32px; font-weight: 600; }
    
    /* Upload Zone */
    .upload-zone {
        border: 2px dashed var(--border); border-radius: var(--radius-lg); padding: 40px 20px;
        text-align: center; cursor: pointer; transition: all var(--transition); background: var(--surface-secondary);
    }
    .upload-zone:hover, .upload-zone.dragover { border-color: var(--primary); background: var(--primary-50); }
    .upload-zone-icon { font-size: 40px; color: var(--primary-400); margin-bottom: 16px; }
    
    .upload-preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 12px; margin-top: 20px; }
    .upload-preview { position: relative; border-radius: var(--radius-md); overflow: hidden; aspect-ratio: 1; border: 1px solid var(--border-light); }
    .upload-preview img { width: 100%; height: 100%; object-fit: cover; }
    .remove-btn {
        position: absolute; top: 4px; right: 4px; width: 24px; height: 24px; border-radius: 50%;
        background: rgba(239, 68, 68, 0.9); color: white; border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center; font-size: 12px;
    }
</style>

<div class="page-header mb-4">
    <div class="page-header-row">
        <div>
            <h1 class="page-title">Buat Laporan Penjualan</h1>
            <p class="page-subtitle">Formulir modern untuk mencatat omzet harian Anda</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-lg-5">
        <div class="wizard-steps" id="wizardSteps">
            <div class="wizard-step active" data-step="1">
                <div class="wizard-step-circle">1</div>
                <div class="fw-semibold mt-2" style="font-size: 14px;">Toko & Waktu</div>
            </div>
            <div class="wizard-step" data-step="2">
                <div class="wizard-step-circle">2</div>
                <div class="fw-semibold mt-2" style="font-size: 14px;">Produk</div>
            </div>
            <div class="wizard-step" data-step="3">
                <div class="wizard-step-circle">3</div>
                <div class="fw-semibold mt-2" style="font-size: 14px;">Foto Bukti</div>
            </div>
            <div class="wizard-step" data-step="4">
                <div class="wizard-step-circle">4</div>
                <div class="fw-semibold mt-2" style="font-size: 14px;">Review</div>
            </div>
        </div>

        <form action="{{ route('karyawan.reports.store') }}" method="POST" enctype="multipart/form-data" id="reportForm">
            @csrf
            
            <!-- Step 1 -->
            <div class="step-content active" id="step1">
                <h4 class="fw-bold mb-4">Informasi Toko & Waktu</h4>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="form-label">Pilih Toko <span class="required">*</span></label>
                        <select name="store_id" id="storeSelect" class="form-select" required>
                            <option value="">-- Pilih Toko --</option>
                            @foreach($stores as $s) 
                                <option value="{{ $s->id }}">{{ $s->name }}</option> 
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Tanggal & Waktu</label>
                        <input type="text" class="form-control" value="{{ now()->format('d F Y, H:i') }}" disabled>
                        <div class="form-hint">Otomatis sesuai waktu saat ini</div>
                    </div>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="step-content" id="step2">
                <h4 class="fw-bold mb-4">Pilih Produk Terjual</h4>
                <div class="product-search-container">
                    <input type="text" id="productSearch" class="form-control" placeholder="Ketik nama produk untuk mencari..." autocomplete="off">
                    <div class="product-dropdown" id="productDropdown">
                        <!-- Populated via JS -->
                    </div>
                </div>
                
                <div id="selectedProductsContainer" class="mb-4">
                    <!-- Selected products appear here -->
                    <div class="empty-state py-4" id="emptyProductsState">
                        <div class="empty-state-icon" style="width: 60px; height: 60px; font-size: 24px; margin-bottom: 16px;"><i class="fa-solid fa-box-open"></i></div>
                        <div class="empty-state-title" style="font-size: 16px;">Belum ada produk</div>
                        <div class="empty-state-desc" style="font-size: 13px;">Cari dan pilih produk di atas.</div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center p-3 bg-primary-50 rounded" style="border-radius: var(--radius-md);">
                    <div class="fw-bold text-primary-700">Total Penjualan:</div>
                    <div class="fw-bold text-primary-700 fs-5" id="step2Total">Rp 0</div>
                </div>
                
                <!-- Hidden container for actual form inputs -->
                <div id="hiddenInputsContainer"></div>
            </div>

            <!-- Step 3 -->
            <div class="step-content" id="step3">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0">Upload Foto Bukti</h4>
                    <span class="badge badge-primary" id="photoCountBadge">0 / 10 Foto</span>
                </div>
                
                <div class="upload-zone" id="uploadZone">
                    <div class="upload-zone-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                    <div class="fw-bold mb-2">Drag & Drop foto di sini</div>
                    <div class="text-muted mb-3" style="font-size: 13px;">atau</div>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('imagesInput').click()">Pilih dari Komputer</button>
                    <input type="file" id="imagesInput" name="images[]" accept="image/*" multiple style="display:none;">
                </div>
                <div id="imagePreview" class="upload-preview-grid"></div>
            </div>

            <!-- Step 4 -->
            <div class="step-content" id="step4">
                <h4 class="fw-bold mb-4">Review Laporan</h4>
                
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3 text-primary">Detail Produk</h6>
                                <div class="table-responsive mb-0">
                                    <table class="table" style="margin:0;">
                                        <thead><tr><th>Produk</th><th class="text-center">Qty</th><th class="text-end">Subtotal</th></tr></thead>
                                        <tbody id="reviewProductsTable">
                                            <!-- Filled via JS -->
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2" class="fw-bold text-end">Total Akhir</td>
                                                <td class="fw-bold text-primary text-end fs-6" id="reviewTotalAmount">Rp 0</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Keterangan / Catatan (Opsional)</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Contoh: Pembayaran tunai, dll..."></textarea>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card bg-neutral-50 mb-4 border-0">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Ringkasan</h6>
                                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                    <span class="text-muted">Toko</span>
                                    <span class="fw-semibold" id="reviewStoreName">-</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                    <span class="text-muted">Total Item</span>
                                    <span class="fw-semibold" id="reviewTotalItems">0</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Foto Lampiran</span>
                                    <span class="fw-semibold" id="reviewTotalPhotos">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="d-flex justify-content-between align-items-center mt-5 pt-4 border-top">
                <button type="button" class="btn btn-secondary px-4" id="btnPrev" style="display:none;"><i class="fa-solid fa-arrow-left"></i> Sebelumnya</button>
                <div class="ms-auto">
                    <button type="button" class="btn btn-primary px-5" id="btnNext">Selanjutnya <i class="fa-solid fa-arrow-right"></i></button>
                    <button type="submit" class="btn btn-success px-5" id="btnSubmit" style="display:none;"><i class="fa-solid fa-check-circle"></i> Kirim Laporan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const productsData = [
        @foreach($products as $p)
        { id: {{ $p->id }}, name: "{{ addslashes($p->name) }}", price: {{ $p->price }} },
        @endforeach
    ];
    
    // Wizard Logic
    let currentStep = 1;
    const totalSteps = 4;
    
    document.getElementById('btnNext').addEventListener('click', () => {
        if (!validateStep(currentStep)) return;
        if (currentStep < totalSteps) {
            currentStep++;
            updateWizard();
        }
    });
    
    document.getElementById('btnPrev').addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            updateWizard();
        }
    });
    
    function validateStep(step) {
        if (step === 1) {
            const store = document.getElementById('storeSelect').value;
            if (!store) {
                Swal.fire({icon: 'warning', title: 'Oops', text: 'Silakan pilih toko terlebih dahulu!'});
                return false;
            }
        }
        if (step === 2) {
            if (selectedProducts.length === 0) {
                Swal.fire({icon: 'warning', title: 'Oops', text: 'Pilih minimal satu produk!'});
                return false;
            }
        }
        if (step === 3) {
            if (uploadedFiles.length === 0) {
                Swal.fire({icon: 'warning', title: 'Oops', text: 'Upload minimal 1 foto bukti!'});
                return false;
            }
        }
        return true;
    }
    
    function updateWizard() {
        // Update Tabs
        document.querySelectorAll('.wizard-step').forEach(el => {
            const s = parseInt(el.dataset.step);
            el.classList.remove('active', 'completed');
            if (s < currentStep) el.classList.add('completed');
            if (s === currentStep) el.classList.add('active');
        });
        
        // Update Content
        document.querySelectorAll('.step-content').forEach(el => {
            el.classList.remove('active');
        });
        document.getElementById('step' + currentStep).classList.add('active');
        
        // Update Buttons
        document.getElementById('btnPrev').style.display = currentStep > 1 ? 'inline-flex' : 'none';
        
        if (currentStep === totalSteps) {
            document.getElementById('btnNext').style.display = 'none';
            document.getElementById('btnSubmit').style.display = 'inline-flex';
            populateReview();
        } else {
            document.getElementById('btnNext').style.display = 'inline-flex';
            document.getElementById('btnSubmit').style.display = 'none';
        }
    }
    
    // Product Selection Logic
    let selectedProducts = [];
    const searchInput = document.getElementById('productSearch');
    const dropdown = document.getElementById('productDropdown');
    
    searchInput.addEventListener('input', function() {
        const val = this.value.toLowerCase();
        dropdown.innerHTML = '';
        if (!val) {
            dropdown.classList.remove('show');
            return;
        }
        
        const filtered = productsData.filter(p => p.name.toLowerCase().includes(val));
        if (filtered.length === 0) {
            dropdown.innerHTML = '<div class="p-3 text-muted text-center">Produk tidak ditemukan</div>';
        } else {
            filtered.forEach(p => {
                const div = document.createElement('div');
                div.className = 'product-option';
                div.innerHTML = `
                    <span class="fw-semibold">${p.name}</span>
                    <span class="text-primary fw-bold">Rp ${p.price.toLocaleString('id-ID')}</span>
                `;
                div.addEventListener('click', () => {
                    addProduct(p);
                    searchInput.value = '';
                    dropdown.classList.remove('show');
                });
                dropdown.appendChild(div);
            });
        }
        dropdown.classList.add('show');
    });
    
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.product-search-container')) {
            dropdown.classList.remove('show');
        }
    });
    
    function addProduct(product) {
        const existing = selectedProducts.find(p => p.id === product.id);
        if (existing) {
            existing.qty++;
        } else {
            selectedProducts.push({...product, qty: 1});
        }
        renderSelectedProducts();
    }
    
    window.updateQty = function(id, delta) {
        const item = selectedProducts.find(p => p.id === id);
        if (item) {
            item.qty += delta;
            if (item.qty <= 0) {
                selectedProducts = selectedProducts.filter(p => p.id !== id);
            }
            renderSelectedProducts();
        }
    };
    
    function renderSelectedProducts() {
        const container = document.getElementById('selectedProductsContainer');
        const emptyState = document.getElementById('emptyProductsState');
        const hiddenContainer = document.getElementById('hiddenInputsContainer');
        
        if (selectedProducts.length === 0) {
            container.innerHTML = '';
            container.appendChild(emptyState);
            emptyState.style.display = 'flex';
            document.getElementById('step2Total').innerText = 'Rp 0';
            hiddenContainer.innerHTML = '';
            return;
        }
        
        emptyState.style.display = 'none';
        container.innerHTML = '';
        hiddenContainer.innerHTML = '';
        
        let total = 0;
        
        selectedProducts.forEach((p, index) => {
            const subtotal = p.price * p.qty;
            total += subtotal;
            
            // Render UI Card
            const card = document.createElement('div');
            card.className = 'selected-product-card';
            card.innerHTML = `
                <div>
                    <div class="fw-bold">${p.name}</div>
                    <div class="text-muted" style="font-size: 13px;">Rp ${p.price.toLocaleString('id-ID')}</div>
                </div>
                <div class="d-flex align-items-center gap-4">
                    <div class="qty-control">
                        <button type="button" class="qty-btn" onclick="updateQty(${p.id}, -1)"><i class="fa-solid fa-minus"></i></button>
                        <input type="text" class="qty-input" value="${p.qty}" readonly>
                        <button type="button" class="qty-btn" onclick="updateQty(${p.id}, 1)"><i class="fa-solid fa-plus"></i></button>
                    </div>
                    <div class="fw-bold text-primary" style="min-width: 100px; text-align: right;">
                        Rp ${subtotal.toLocaleString('id-ID')}
                    </div>
                    <button type="button" class="btn btn-ghost btn-icon-sm text-danger px-2" onclick="updateQty(${p.id}, -${p.qty})">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            `;
            container.appendChild(card);
            
            // Render hidden inputs for form submission
            hiddenContainer.innerHTML += `
                <input type="hidden" name="products[${index}][id]" value="${p.id}">
                <input type="hidden" name="products[${index}][qty]" value="${p.qty}">
            `;
        });
        
        document.getElementById('step2Total').innerText = `Rp ${total.toLocaleString('id-ID')}`;
    }
    
    // Image Upload Logic
    let uploadedFiles = [];
    let fileIdCounter = 0;
    const uploadZone = document.getElementById('uploadZone');
    const imagesInput = document.getElementById('imagesInput');
    
    uploadZone.addEventListener('dragover', (e) => { e.preventDefault(); uploadZone.classList.add('dragover'); });
    uploadZone.addEventListener('dragleave', () => { uploadZone.classList.remove('dragover'); });
    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
    });
    imagesInput.addEventListener('change', (e) => { handleFiles(e.target.files); });
    
    function handleFiles(files) {
        const remaining = 10 - uploadedFiles.length;
        if (files.length > remaining) {
            Swal.fire({icon:'warning', title:'Maksimal 10 foto', text:'Anda hanya bisa upload maksimal 10 foto.', customClass:{popup:'rounded-4'}});
        }
        for (let i = 0; i < Math.min(files.length, remaining); i++) {
            const file = files[i];
            if (!file.type.startsWith('image/')) continue;
            
            const id = fileIdCounter++;
            uploadedFiles.push({id, file});
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                const div = document.createElement('div');
                div.className = 'upload-preview';
                div.dataset.fileId = id;
                div.innerHTML = `<img src="${e.target.result}"><button type="button" class="remove-btn" onclick="removeFile(${id})"><i class="fa-solid fa-xmark"></i></button>`;
                preview.appendChild(div);
                updatePhotoCount();
            };
            reader.readAsDataURL(file);
        }
        updateFileInput();
    }
    
    window.removeFile = function(id) {
        uploadedFiles = uploadedFiles.filter(f => f.id !== id);
        document.querySelector(`.upload-preview[data-file-id="${id}"]`)?.remove();
        updatePhotoCount();
        updateFileInput();
    };
    
    function updatePhotoCount() {
        document.getElementById('photoCountBadge').innerText = `${uploadedFiles.length} / 10 Foto`;
    }
    
    function updateFileInput() {
        const dt = new DataTransfer();
        uploadedFiles.forEach(f => dt.items.add(f.file));
        document.getElementById('imagesInput').files = dt.files;
    }
    
    // Review logic
    function populateReview() {
        // Store
        const storeSelect = document.getElementById('storeSelect');
        document.getElementById('reviewStoreName').innerText = storeSelect.options[storeSelect.selectedIndex].text;
        
        // Products
        const tbody = document.getElementById('reviewProductsTable');
        tbody.innerHTML = '';
        let totalItems = 0;
        let totalAmount = 0;
        
        selectedProducts.forEach(p => {
            const subtotal = p.price * p.qty;
            totalItems += p.qty;
            totalAmount += subtotal;
            
            tbody.innerHTML += `
                <tr>
                    <td>${p.name}</td>
                    <td class="text-center">${p.qty}</td>
                    <td class="text-end">Rp ${subtotal.toLocaleString('id-ID')}</td>
                </tr>
            `;
        });
        
        document.getElementById('reviewTotalItems').innerText = totalItems;
        document.getElementById('reviewTotalAmount').innerText = `Rp ${totalAmount.toLocaleString('id-ID')}`;
        document.getElementById('reviewTotalPhotos').innerText = uploadedFiles.length;
    }
    
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        document.getElementById('btnSubmit').disabled = true;
        document.getElementById('btnSubmit').innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengirim...';
    });
</script>
@endsection
