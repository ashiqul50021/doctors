@extends('layouts.admin')

@include('ecommerce::backend.products.partials.image-manager-styles')

@section('title', 'Add Product - abcsheba Admin')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Add Product</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.admin.products.index') }}">Products</a></li>
                <li class="breadcrumb-item active">Add Product</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        @php
            $variantRows = collect(old('variants', []))->map(function ($variant) {
                return [
                    'id' => $variant['id'] ?? '',
                    'option_name' => $variant['option_name'] ?? '',
                    'option_value' => $variant['option_value'] ?? '',
                    'price' => $variant['price'] ?? '',
                    'sale_price' => $variant['sale_price'] ?? '',
                    'stock' => $variant['stock'] ?? '',
                    'sku' => $variant['sku'] ?? '',
                    'is_active' => !array_key_exists('is_active', $variant) || (bool) $variant['is_active'],
                ];
            })->all();
        @endphp
        <div class="card">
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Tab Navigation Links -->
                <ul class="nav nav-tabs nav-tabs-solid nav-justified mb-4" id="productFormTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="general-tab" data-bs-toggle="tab" href="#general-pane" role="tab" aria-controls="general-pane" aria-selected="true">
                            <i class="fas fa-info-circle me-1"></i> General Info
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pricing-tab" data-bs-toggle="tab" href="#pricing-pane" role="tab" aria-controls="pricing-pane" aria-selected="false">
                            <i class="fas fa-coins me-1"></i> Pricing & Stock
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="landing-tab" data-bs-toggle="tab" href="#landing-pane" role="tab" aria-controls="landing-pane" aria-selected="false">
                            <i class="fas fa-layer-group me-1"></i> Landing Page Builder
                        </a>
                    </li>
                </ul>

                <form action="{{ route('ecommerce.admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productMainForm">
                    @csrf
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="card p-3 mt-3 border bg-light shadow-sm" style="border-radius: 12px; border: 1px solid #cbd5e1 !important;">
                                                <label class="fw-bold mb-2 text-dark">নতুন সেকশন যোগ করুন (Add New Section)</label>
                                                <div class="d-flex gap-2">
                                                    <select id="new-section-type" class="form-control form-control-sm form-select" style="max-width: 250px;">
                                                        <option value="features">Product Features (ফিচারসমূহ)</option>
                                                        <option value="badges">Trust Badges (পণ্যের ট্রাস্ট ব্যাজসমূহ)</option>
                                                        <option value="problems">User Problems (সমস্যাসমূহ)</option>
                                                        <option value="benefits">Product Benefits (সুবিধাসমূহ)</option>
                                                        <option value="package">Package Includes (প্যাকেজে কী পাবেন)</option>
                                                        <option value="faq">FAQ (প্রশ্ন ও উত্তর)</option>
                                                        <option value="video">Showcase Video (ইউটিউব ভিডিও)</option>
                                                        <option value="gallery">Gallery Showcase (বাস্তব ছবি গ্যালারি)</option>
                                                        <option value="trust">Why Choose Us (কেন আমাদের থেকে কিনবেন)</option>
                                                        <option value="cta">Buy Now Button (অর্ডার বাটন)</option>
                                                         <option value="custom">Custom Section (কাস্টম সেকশন)</option>
                                                    </select>
                                                    <button type="button" id="add-section-btn" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> সেকশন যোগ করুন (Add Section)</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @include('ecommerce::backend.products.partials.variant-manager', ['variantRows' => $variantRows])
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            initializeProductImageUpload({
                inputId: 'productImageInput',
                previewId: 'productImagePreview',
                previewContainerId: 'productImagePreviewContainer',
                helperId: 'productImageHelper',
                emptyMessage: 'Select the main product image. Large files will be compressed automatically before upload.'
            });

            initializeInteractiveGalleryUploader({
                gridId: 'interactiveGalleryGrid',
                inputsContainerId: 'galleryInputsContainer'
            });

            initializeVariantManager();

            const hasVariantsToggle = document.getElementById('has_variants');
            const variantManagerContainer = document.getElementById('variant-manager-container');

            function toggleVariantFields() {
                const isChecked = hasVariantsToggle.checked;
                variantManagerContainer.style.display = isChecked ? 'block' : 'none';
                
                const variantInputs = variantManagerContainer.querySelectorAll('input, select, textarea');
                if (isChecked) {
                    variantInputs.forEach(el => {
                        el.disabled = false;
                    });
                } else {
                    variantInputs.forEach(el => {
                        el.disabled = true;
                    });
                }
            }

            if (hasVariantsToggle && variantManagerContainer) {
                hasVariantsToggle.addEventListener('change', toggleVariantFields);
                toggleVariantFields();
            }

            // Unified Sections Builder
            const sectionsContainer = document.getElementById('landing-sections-builder-container');
            const addSectionBtn = document.getElementById('add-section-btn');
            const newSectionTypeSelect = document.getElementById('new-section-type');

            function getSectionCounter() {
                return document.querySelectorAll('.section-card').length;
            }

            function createSectionItemRow(secIndex, value = '') {
                const div = document.createElement('div');
                div.className = 'item-row d-flex align-items-center gap-2 mb-2';
                div.innerHTML = `
                    <textarea name="landing_settings[sections][${secIndex}][items][]" class="form-control form-control-sm" rows="2" placeholder="আইটেমের বিবরণ লিখুন">${value}</textarea>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn"><i class="fas fa-trash"></i></button>
                `;
                const removeBtn = div.querySelector('.remove-item-btn');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        div.remove();
                    });
                }
                return div;
            }

            function createFAQItemRow(secIndex, faqIndex, q = '', a = '') {
                const div = document.createElement('div');
                div.className = 'faq-item-row border p-2 mb-2 bg-white rounded shadow-sm';
                div.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-bold text-muted">প্রশ্ন ও উত্তর #${faqIndex + 1}</span>
                        <button type="button" class="btn btn-xs btn-outline-danger remove-item-btn"><i class="fas fa-trash"></i></button>
                    </div>
                    <div class="form-group mb-1">
                        <input type="text" name="landing_settings[sections][${secIndex}][faqs][${faqIndex}][q]" class="form-control form-control-sm" value="${q}" placeholder="প্রশ্ন লিখুন">
                    </div>
                    <div class="form-group mb-0">
                        <textarea name="landing_settings[sections][${secIndex}][faqs][${faqIndex}][a]" class="form-control form-control-sm" rows="2" placeholder="উত্তর লিখুন">${a}</textarea>
                    </div>
                `;
                const removeBtn = div.querySelector('.remove-item-btn');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        div.remove();
                        const card = div.closest('.section-card');
                        if (card) reindexFAQItems(card);
                    });
                }
                return div;
            }

            function reindexFAQItems(sectionCard) {
                const secIndex = parseInt(sectionCard.getAttribute('data-section-index') || '0');
                const faqRows = sectionCard.querySelectorAll('.faq-item-row');
                faqRows.forEach((row, faqIndex) => {
                    const textMuted = row.querySelector('.text-muted');
                    if (textMuted) textMuted.textContent = `প্রশ্ন ও উত্তর #${faqIndex + 1}`;
                    const qInput = row.querySelector('input');
                    if (qInput) qInput.name = `landing_settings[sections][${secIndex}][faqs][${faqIndex}][q]`;
                    const aTextarea = row.querySelector('textarea');
                    if (aTextarea) aTextarea.name = `landing_settings[sections][${secIndex}][faqs][${faqIndex}][a]`;
                });
            }

            function createBadgeItemRow(secIndex, badgeIndex, icon = '', title = '', desc = '') {
                const div = document.createElement('div');
                div.className = 'badge-item-row border p-2 mb-2 bg-white rounded shadow-sm';
                div.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-bold text-muted">ব্যাজ #${badgeIndex + 1}</span>
                        <button type="button" class="btn btn-xs btn-outline-danger remove-item-btn"><i class="fas fa-trash"></i></button>
                    </div>
                    <div class="row g-1">
                        <div class="col-4">
                            <input type="text" name="landing_settings[sections][${secIndex}][badges][${badgeIndex}][icon]" class="form-control form-control-sm" value="${icon}" placeholder="আইকন ক্লাস">
                        </div>
                        <div class="col-4">
                            <input type="text" name="landing_settings[sections][${secIndex}][badges][${badgeIndex}][title]" class="form-control form-control-sm" value="${title}" placeholder="টাইটেল">
                        </div>
                        <div class="col-4">
                            <input type="text" name="landing_settings[sections][${secIndex}][badges][${badgeIndex}][desc]" class="form-control form-control-sm" value="${desc}" placeholder="বর্ণনা">
                        </div>
                    </div>
                `;
                const removeBtn = div.querySelector('.remove-item-btn');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        div.remove();
                        const card = div.closest('.section-card');
                        if (card) reindexBadgeItems(card);
                    });
                }
                return div;
            }

            function reindexBadgeItems(sectionCard) {
                const secIndex = parseInt(sectionCard.getAttribute('data-section-index') || '0');
                const rows = sectionCard.querySelectorAll('.badge-item-row');
                rows.forEach((row, idx) => {
                    const textMuted = row.querySelector('.text-muted');
                    if (textMuted) textMuted.textContent = `ব্যাজ #${idx + 1}`;
                    
                    const iconInput = row.querySelector('input[name*="[icon]"]');
                    if (iconInput) iconInput.name = `landing_settings[sections][${secIndex}][badges][${idx}][icon]`;
                    
                    const titleInput = row.querySelector('input[name*="[title]"]');
                    if (titleInput) titleInput.name = `landing_settings[sections][${secIndex}][badges][${idx}][title]`;
                    
                    const descInput = row.querySelector('input[name*="[desc]"]');
                    if (descInput) descInput.name = `landing_settings[sections][${secIndex}][badges][${idx}][desc]`;
                });
            }

            function createTrustFeatureItemRow(secIndex, featureIndex, title = '', desc = '') {
                const div = document.createElement('div');
                div.className = 'trust-feature-item-row border p-2 mb-2 bg-white rounded shadow-sm';
                div.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-bold text-muted">ফিচার #${featureIndex + 1}</span>
                        <button type="button" class="btn btn-xs btn-outline-danger remove-item-btn"><i class="fas fa-trash"></i></button>
                    </div>
                    <div class="form-group mb-1">
                        <input type="text" name="landing_settings[sections][${secIndex}][trust_features][${featureIndex}][title]" class="form-control form-control-sm" value="${title}" placeholder="টাইটেল লিখুন">
                    </div>
                    <div class="form-group mb-0">
                        <textarea name="landing_settings[sections][${secIndex}][trust_features][${featureIndex}][desc]" class="form-control form-control-sm" rows="2" placeholder="বর্ণনা লিখুন">${desc}</textarea>
                    </div>
                `;
                const removeBtn = div.querySelector('.remove-item-btn');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        div.remove();
                        const card = div.closest('.section-card');
                        if (card) reindexTrustFeatureItems(card);
                    });
                }
                return div;
            }

            function reindexTrustFeatureItems(sectionCard) {
                const secIndex = parseInt(sectionCard.getAttribute('data-section-index') || '0');
                const rows = sectionCard.querySelectorAll('.trust-feature-item-row');
                rows.forEach((row, idx) => {
                    const textMuted = row.querySelector('.text-muted');
                    if (textMuted) textMuted.textContent = `ফিচার #${idx + 1}`;
                    
                    const titleInput = row.querySelector('input[name*="[title]"]');
                    if (titleInput) titleInput.name = `landing_settings[sections][${secIndex}][trust_features][${idx}][title]`;
                    
                    const descInput = row.querySelector('textarea[name*="[desc]"]');
                    if (descInput) descInput.name = `landing_settings[sections][${secIndex}][trust_features][${idx}][desc]`;
                });
            }

            function createSectionCard(type, index) {
                const card = document.createElement('div');
                card.className = 'section-card card p-3 mb-3 border shadow-sm';
                card.style.borderRadius = '12px';
                card.style.border = '1px solid #cbd5e1';
                card.style.backgroundColor = '#f8fafc';
                card.setAttribute('data-section-type', type);
                card.setAttribute('data-section-index', index);

                const titles = {
                    features: 'আমাদের প্রোডাক্টের বৈশিষ্ট্য',
                    badges: 'আমাদের থেকে কেন কিনবেন?',
                    problems: 'এই সমস্যাগুলো কি আপনারও আছে?',
                    benefits: 'বৈশিষ্ট্যগুলো কি কি জানতে চান?',
                    package: 'প্যাকেজের সাথে যা যা পাবেন',
                    faq: 'কিছু সাধারণ প্রশ্ন',
                    video: 'পণ্যটির বিবরণী ও ব্যবহারবিধি ভিডিও',
                    gallery: 'পণ্যটির কিছু বাস্তব ছবি (Real Gallery)',
                    trust: 'কেন আমাদের থেকে অর্ডার করবেন?',
                    cta: 'অর্ডার করতে এখানে ক্লিক করুন',
                    custom: 'নতুন কাস্টম সেকশন'
                };
                const tags = {
                    features: 'Product Features',
                    badges: 'Trust Badges',
                    problems: 'Common Issues',
                    benefits: 'Benefits',
                    package: 'Package Contents',
                    faq: 'FAQs',
                    video: 'Showcase Video',
                    gallery: 'Showcase',
                    trust: 'Trust',
                    cta: 'Call To Action',
                    custom: 'INFO'
                };
                const styles = {
                    features: 'blue-check',
                    badges: 'blue-check',
                    problems: 'red-cross',
                    benefits: 'green-check',
                    package: 'package-box',
                    faq: 'faq-accordion',
                    video: 'blue-check',
                    gallery: 'blue-check',
                    trust: 'blue-check',
                    cta: 'blue-check',
                    custom: 'blue-check'
                };

                card.innerHTML = `
                    <input type="hidden" name="landing_settings[sections][${index}][type]" value="${type}">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-dark text-white p-2">সেকশন #<span class="sec-number">${index + 1}</span></span>
                            <span class="badge bg-info text-white p-2">${type.toUpperCase()}</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="btn btn-xs btn-outline-secondary move-up-btn" title="উপরে তুলুন"><i class="fas fa-arrow-up"></i></button>
                            <button type="button" class="btn btn-xs btn-outline-secondary move-down-btn" title="নিচে নামান"><i class="fas fa-arrow-down"></i></button>
                            <button type="button" class="btn btn-xs btn-danger remove-section-btn ms-2" title="ডিলিট"><i class="fas fa-trash"></i> ডিলিট</button>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-6 col-12">
                            <div class="form-group mb-2">
                                <label class="small fw-bold">সেকশন টাইটেল (Title)</label>
                                <input type="text" name="landing_settings[sections][${index}][title]" class="form-control form-control-sm" value="${titles[type]}" placeholder="টাইটেল লিখুন">
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-group mb-2">
                                <label class="small fw-bold">সেকশন ট্যাগ (Tag)</label>
                                <input type="text" name="landing_settings[sections][${index}][tag]" class="form-control form-control-sm" value="${tags[type]}" placeholder="ট্যাগ লিখুন">
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-group mb-2">
                                <label class="small fw-bold">স্টাইল / লেআউট (Style)</label>
                                <select name="landing_settings[sections][${index}][style]" class="form-control form-control-sm form-select">
                                    <option value="blue-check" ${styles[type] === 'blue-check' ? 'selected' : ''}>Blue Check Square</option>
                                    <option value="green-check" ${styles[type] === 'green-check' ? 'selected' : ''}>Green Check Circle</option>
                                    <option value="red-cross" ${styles[type] === 'red-cross' ? 'selected' : ''}>Red Cross</option>
                                    <option value="yellow-star" ${styles[type] === 'yellow-star' ? 'selected' : ''}>Yellow Star</option>
                                    <option value="orange-info" ${styles[type] === 'orange-info' ? 'selected' : ''}>Orange Info</option>
                                    <option value="package-box" ${styles[type] === 'package-box' ? 'selected' : ''}>Package Box Style</option>
                                    <option value="faq-accordion" ${styles[type] === 'faq-accordion' ? 'selected' : ''}>FAQ Accordion</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="section-content-container mt-2 border-top pt-2" style="${['video', 'gallery', 'cta'].includes(type) ? 'display: none;' : ''}">
                        <label class="small fw-bold mb-2">
                            ${type === 'faq' ? 'প্রশ্ন ও উত্তরসমূহ (FAQs)' : (type === 'badges' ? 'পণ্যের ট্রাস্ট ব্যাজসমূহ (Badges)' : (type === 'trust' ? 'কেন আমাদের থেকে সংগ্রহ করবেন (Trust Features)' : 'সেকশন আইটেমসমূহ (Items)'))}
                        </label>
                        <div class="items-list"></div>
                        <button type="button" class="btn btn-xs btn-outline-primary add-item-btn mt-1">
                            <i class="fas fa-plus me-1"></i>
                            ${type === 'faq' ? 'প্রশ্ন ও উত্তর যোগ করুন (Add FAQ)' : (type === 'badges' ? 'ব্যাজ যোগ করুন (Add Badge)' : (type === 'trust' ? 'ফিচার যোগ করুন (Add Feature)' : 'আইটেম যোগ করুন (Add Item)'))}
                        </button>
                    </div>
                `;

                bindSectionCardEvents(card);
                return card;
            }

            function bindSectionCardEvents(card) {
                const type = card.getAttribute('data-section-type');

                // Delete section
                const removeBtn = card.querySelector('.remove-section-btn');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        card.remove();
                        reindexSections();
                    });
                }

                // Move up
                const moveUpBtn = card.querySelector('.move-up-btn');
                if (moveUpBtn) {
                    moveUpBtn.addEventListener('click', function() {
                        const prev = card.previousElementSibling;
                        if (prev) {
                            card.parentNode.insertBefore(card, prev);
                            reindexSections();
                        }
                    });
                }

                // Move down
                const moveDownBtn = card.querySelector('.move-down-btn');
                if (moveDownBtn) {
                    moveDownBtn.addEventListener('click', function() {
                        const next = card.nextElementSibling;
                        if (next) {
                            card.parentNode.insertBefore(next, card);
                            reindexSections();
                        }
                    });
                }

                // Add item
                const addItemBtn = card.querySelector('.add-item-btn');
                if (addItemBtn) {
                    addItemBtn.addEventListener('click', function() {
                        const secIndex = parseInt(card.getAttribute('data-section-index') || '0');
                        const itemsList = card.querySelector('.items-list');
                        if (!itemsList) return;
                        
                        if (type === 'faq') {
                            const faqIndex = itemsList.querySelectorAll('.faq-item-row').length;
                            itemsList.appendChild(createFAQItemRow(secIndex, faqIndex));
                            const qInput = itemsList.lastElementChild.querySelector('input');
                            if (qInput) qInput.focus();
                        } else if (type === 'badges') {
                            const badgeIndex = itemsList.querySelectorAll('.badge-item-row').length;
                            itemsList.appendChild(createBadgeItemRow(secIndex, badgeIndex));
                            const iconInput = itemsList.lastElementChild.querySelector('input');
                            if (iconInput) iconInput.focus();
                        } else if (type === 'trust') {
                            const featureIndex = itemsList.querySelectorAll('.trust-feature-item-row').length;
                            itemsList.appendChild(createTrustFeatureItemRow(secIndex, featureIndex));
                            const titleInput = itemsList.lastElementChild.querySelector('input');
                            if (titleInput) titleInput.focus();
                        } else {
                            itemsList.appendChild(createSectionItemRow(secIndex));
                            const textarea = itemsList.lastElementChild.querySelector('textarea');
                            if (textarea) textarea.focus();
                        }
                    });
                }

                // Bind existing inner delete buttons
                card.querySelectorAll('.remove-item-btn').forEach((btn) => {
                    btn.addEventListener('click', function() {
                        const row = btn.closest('.item-row, .faq-item-row, .badge-item-row, .trust-feature-item-row');
                        if (row) {
                            row.remove();
                            if (type === 'faq') {
                                reindexFAQItems(card);
                            } else if (type === 'badges') {
                                reindexBadgeItems(card);
                            } else if (type === 'trust') {
                                reindexTrustFeatureItems(card);
                            }
                        }
                    });
                });
            }

            function reindexSections() {
                const cards = document.querySelectorAll('.section-card');
                cards.forEach((card, index) => {
                    card.setAttribute('data-section-index', index);
                    
                    const secNumSpan = card.querySelector('.sec-number');
                    if (secNumSpan) secNumSpan.textContent = index + 1;

                    const typeInput = card.querySelector('input[type="hidden"]');
                    if (typeInput) typeInput.name = `landing_settings[sections][${index}][type]`;

                    const titleInput = card.querySelector('input[name*="[title]"]');
                    if (titleInput) titleInput.name = `landing_settings[sections][${index}][title]`;

                    const tagInput = card.querySelector('input[name*="[tag]"]');
                    if (tagInput) tagInput.name = `landing_settings[sections][${index}][tag]`;

                    const styleSelect = card.querySelector('select[name*="[style]"]');
                    if (styleSelect) styleSelect.name = `landing_settings[sections][${index}][style]`;

                    const type = card.getAttribute('data-section-type');
                    if (type === 'faq') {
                        reindexFAQItems(card);
                    } else if (type === 'badges') {
                        reindexBadgeItems(card);
                    } else if (type === 'trust') {
                        reindexTrustFeatureItems(card);
                    } else {
                        card.querySelectorAll('.item-row textarea').forEach(textarea => {
                            textarea.name = `landing_settings[sections][${index}][items][]`;
                        });
                    }

                    const addBtn = card.querySelector('.add-item-btn');
                    if (addBtn) {
                        const newAddBtn = addBtn.cloneNode(true);
                        addBtn.parentNode.replaceChild(newAddBtn, addBtn);
                        newAddBtn.addEventListener('click', function() {
                            const itemsList = card.querySelector('.items-list');
                            if (!itemsList) return;
                            
                            if (type === 'faq') {
                                const faqIndex = itemsList.querySelectorAll('.faq-item-row').length;
                                itemsList.appendChild(createFAQItemRow(index, faqIndex));
                                const qInput = itemsList.lastElementChild.querySelector('input');
                                if (qInput) qInput.focus();
                            } else if (type === 'badges') {
                                const badgeIndex = itemsList.querySelectorAll('.badge-item-row').length;
                                itemsList.appendChild(createBadgeItemRow(index, badgeIndex));
                                const iconInput = itemsList.lastElementChild.querySelector('input');
                                if (iconInput) iconInput.focus();
                            } else if (type === 'trust') {
                                const featureIndex = itemsList.querySelectorAll('.trust-feature-item-row').length;
                                itemsList.appendChild(createTrustFeatureItemRow(index, featureIndex));
                                const titleInput = itemsList.lastElementChild.querySelector('input');
                                if (titleInput) titleInput.focus();
                            } else {
                                itemsList.appendChild(createSectionItemRow(index));
                                const textarea = itemsList.lastElementChild.querySelector('textarea');
                                if (textarea) textarea.focus();
                            }
                        });
                    }

                    const moveUpBtn = card.querySelector('.move-up-btn');
                    if (moveUpBtn) moveUpBtn.disabled = (index === 0);
                    
                    const moveDownBtn = card.querySelector('.move-down-btn');
                    if (moveDownBtn) moveDownBtn.disabled = (index === cards.length - 1);
                });
            }

            document.querySelectorAll('.section-card').forEach(card => {
                bindSectionCardEvents(card);
            });
            reindexSections();

            if (addSectionBtn && sectionsContainer) {
                addSectionBtn.addEventListener('click', function() {
                    const type = newSectionTypeSelect.value;
                    const index = getSectionCounter();
                    const newCard = createSectionCard(type, index);
                    sectionsContainer.appendChild(newCard);
                    reindexSections();
                    const titleInput = newCard.querySelector('input[type="text"]');
                    if (titleInput) titleInput.focus();
                });
            }
        });

        function initializeProductImageUpload({ inputId, previewId, previewContainerId, helperId, emptyMessage }) {
            const fileInput = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const previewContainer = document.getElementById(previewContainerId);
            const helper = document.getElementById(helperId);

            if (!fileInput || !preview || !previewContainer || !helper) {
                return;
            }

            fileInput.addEventListener('change', function (event) {
                const file = event.target.files[0];

                if (!file) {
                    preview.src = '#';
                    previewContainer.style.display = 'none';
                    helper.textContent = emptyMessage;
                    return;
                }

                if (!file.type.startsWith('image/')) {
                    helper.textContent = 'Please select a valid image file.';
                    event.target.value = '';
                    preview.src = '#';
                    previewContainer.style.display = 'none';
                    return;
                }

                updatePreview(preview, previewContainer, file);
                helper.innerHTML = `<span class="text-success">Image selected. Size: ${(file.size / 1024).toFixed(2)} KB.</span>`;
            });
        }

        function updatePreview(preview, previewContainer, file) {
            preview.src = URL.createObjectURL(file);
            previewContainer.style.display = 'block';
        }

        function initializeInteractiveGalleryUploader({ gridId, inputsContainerId }) {
            const grid = document.getElementById(gridId);
            const inputsContainer = document.getElementById(inputsContainerId);

            if (!grid || !inputsContainer) return;

            inputsContainer.innerHTML = '';
            grid.innerHTML = '';

            let imageCounter = 0;

            function createAddCard() {
                const addCard = document.createElement('div');
                addCard.className = 'gallery-add-card';
                addCard.innerHTML = `
                    <i class="fas fa-plus-circle"></i>
                    <span>Add Image</span>
                `;

                addCard.addEventListener('click', function () {
                    imageCounter++;

                    const fileInput = document.createElement('input');
                    fileInput.type = 'file';
                    fileInput.name = 'gallery[]';
                    fileInput.accept = 'image/*';
                    fileInput.style.display = 'none';
                    fileInput.id = `gallery_input_${imageCounter}`;

                    fileInput.addEventListener('change', function (event) {
                        const file = event.target.files[0];
                        if (!file) {
                            fileInput.remove();
                            return;
                        }

                        if (!file.type.startsWith('image/')) {
                            alert('Please select a valid image file.');
                            fileInput.remove();
                            return;
                        }

                        inputsContainer.appendChild(fileInput);

                        const previewCard = document.createElement('div');
                        previewCard.className = 'gallery-preview-card';

                        const objectUrl = URL.createObjectURL(file);
                        previewCard.innerHTML = `
                            <img src="${objectUrl}" alt="${file.name}">
                            <div class="gallery-preview-meta">
                                <strong>${file.name}</strong><br>
                                ${(file.size / 1024).toFixed(1)} KB
                            </div>
                            <div class="gallery-preview-actions">
                                <button type="button" class="btn btn-sm btn-outline-danger w-100 js-remove-preview">
                                    Remove
                                </button>
                            </div>
                        `;

                        const img = previewCard.querySelector('img');
                        img.addEventListener('load', function() {
                            URL.revokeObjectURL(objectUrl);
                        }, { once: true });

                        previewCard.querySelector('.js-remove-preview').addEventListener('click', function() {
                            previewCard.remove();
                            fileInput.remove();
                        });

                        grid.insertBefore(previewCard, addCard);
                    });

                    fileInput.click();
                });

                grid.appendChild(addCard);
            }

            createAddCard();
        }

        function initializeExistingGalleryRemoval({ sectionId, gridId, removedInputsContainerId }) {
            const section = document.getElementById(sectionId);
            const grid = document.getElementById(gridId);
            const removedInputsContainer = document.getElementById(removedInputsContainerId);

            if (!section || !grid || !removedInputsContainer) {
                return;
            }

            grid.addEventListener('click', function (event) {
                const removeButton = event.target.closest('.js-remove-existing-gallery');

                if (!removeButton) {
                    return;
                }

                const path = removeButton.dataset.path;
                const card = removeButton.closest('[data-gallery-path]');

                if (!path || !card) {
                    return;
                }

                const alreadyRemoved = Array.from(removedInputsContainer.querySelectorAll('input[name="removed_gallery[]"]'))
                    .some((input) => input.value === path);

                if (!alreadyRemoved) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'removed_gallery[]';
                    input.value = path;
                    removedInputsContainer.appendChild(input);
                }

                card.remove();

                if (!grid.children.length) {
                    section.style.display = 'none';
                }
            });
        }

        function initializeVariantManager() {
            const rowsContainer = document.getElementById('variantRows');
            const template = document.getElementById('variantRowTemplate');
            const addButton = document.getElementById('addVariantRowBtn');

            if (!rowsContainer || !template || !addButton) {
                return;
            }

            let nextIndex = rowsContainer.querySelectorAll('.variant-row').length;

            addButton.addEventListener('click', function () {
                const wrapper = document.createElement('tbody');
                wrapper.innerHTML = template.innerHTML.replace(/__INDEX__/g, nextIndex);
                rowsContainer.appendChild(wrapper.firstElementChild);
                nextIndex += 1;
            });

            rowsContainer.addEventListener('click', function (event) {
                const removeButton = event.target.closest('.js-remove-variant-row');

                if (!removeButton) {
                    return;
                }

                removeButton.closest('.variant-row')?.remove();
            });
        }
    </script>
@endpush
