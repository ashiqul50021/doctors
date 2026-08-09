@push('styles')
<style>
    /* Tab Styles */
    .nav-tabs-solid {
        background-color: #F1F5F9;
        border-radius: 14px;
        padding: 6px;
        border: none;
    }
    .nav-tabs-solid .nav-item {
        margin-bottom: 0;
    }
    .nav-tabs-solid .nav-link {
        border: none;
        border-radius: 10px;
        color: #64748B;
        font-weight: 600;
        padding: 11px 20px;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        text-align: center;
        font-size: 14px;
    }
    .nav-tabs-solid .nav-link:hover {
        color: #0F172A;
        background-color: rgba(255, 255, 255, 0.6);
    }
    .nav-tabs-solid .nav-link.active {
        background-color: #FFFFFF !important;
        color: #4F46E5 !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.02) !important;
    }

    /* Section Builder Cards Overhaul */
    .section-card {
        background: #FFFFFF !important;
        border: 1px solid #E2E8F0 !important;
        border-radius: 16px !important;
        padding: 22px !important;
        margin-bottom: 22px !important;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04) !important;
        transition: all 0.25s ease !important;
    }

    .section-card:hover {
        box-shadow: 0 10px 25px -4px rgba(15, 23, 42, 0.08) !important;
        border-color: #CBD5E1 !important;
    }

    .section-card .bg-dark {
        background: #0F172A !important;
        font-weight: 700 !important;
        letter-spacing: 0.03em !important;
        border-radius: 50px !important;
        padding: 6px 14px !important;
        font-size: 12px !important;
    }

    .section-card .badge.bg-primary {
        background: linear-gradient(135deg, #4F46E5, #2563EB) !important;
        font-weight: 700 !important;
        border-radius: 50px !important;
        padding: 6px 14px !important;
        font-size: 12px !important;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3) !important;
    }

    /* Move Up/Down & Delete Buttons */
    .section-card .btn-light, 
    .section-card button.btn-light,
    .section-card a.btn-light {
        background: #F1F5F9 !important;
        border: 1px solid #E2E8F0 !important;
        color: #475569 !important;
        border-radius: 10px !important;
        font-weight: 700 !important;
        padding: 6px 12px !important;
        transition: all 0.2s ease !important;
    }

    .section-card .btn-light:hover {
        background: #4F46E5 !important;
        color: #FFFFFF !important;
        border-color: #4F46E5 !important;
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3) !important;
    }

    .section-card .btn-danger,
    .section-card button.btn-danger {
        background: linear-gradient(135deg, #F43F5E, #E11D48) !important;
        border: none !important;
        color: #FFFFFF !important;
        border-radius: 10px !important;
        font-weight: 700 !important;
        padding: 7px 16px !important;
        box-shadow: 0 4px 12px rgba(244, 63, 94, 0.3) !important;
        transition: all 0.2s ease !important;
    }

    .section-card .btn-danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(244, 63, 94, 0.4) !important;
        opacity: 0.95;
    }

    /* Form Controls inside Builder */
    .section-card label {
        font-size: 11.5px !important;
        font-weight: 700 !important;
        color: #475569 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        margin-bottom: 6px !important;
    }

    .section-card .form-control,
    .section-card .form-select {
        background-color: #F8FAFC !important;
        border: 1px solid #CBD5E1 !important;
        border-radius: 10px !important;
        padding: 8px 14px !important;
        font-size: 13.5px !important;
        color: #0F172A !important;
        font-weight: 500 !important;
        transition: all 0.2s ease !important;
    }

    .section-card .form-control:focus,
    .section-card .form-select:focus {
        background-color: #FFFFFF !important;
        border-color: #4F46E5 !important;
        box-shadow: 0 0 0 3.5px rgba(79, 70, 229, 0.14) !important;
    }

    /* Color Picker Row Improvements */
    .section-card input[type="color"] {
        width: 42px !important;
        height: 38px !important;
        border-radius: 8px !important;
        border: 1px solid #CBD5E1 !important;
        padding: 3px !important;
        cursor: pointer !important;
        background: #FFFFFF !important;
    }

    /* Repeater Items (Badges, FAQs, Features) */
    .section-card .border.rounded,
    .section-card div[class*="item-row"],
    .section-card .p-2.border {
        background: #F8FAFC !important;
        border: 1px solid #E2E8F0 !important;
        border-radius: 12px !important;
        padding: 14px 16px !important;
        margin-bottom: 12px !important;
        transition: border-color 0.2s !important;
    }

    .section-card .border.rounded:hover {
        border-color: #CBD5E1 !important;
    }

    /* Trash Delete Button inside Repeater Items */
    .section-card .btn-outline-danger,
    .section-card button.btn-outline-danger {
        background: rgba(244, 63, 94, 0.08) !important;
        color: #E11D48 !important;
        border: 1px solid rgba(244, 63, 94, 0.2) !important;
        border-radius: 8px !important;
        padding: 6px 12px !important;
        font-size: 13px !important;
        transition: all 0.2s ease !important;
    }

    .section-card .btn-outline-danger:hover {
        background: #F43F5E !important;
        color: #FFFFFF !important;
        border-color: transparent !important;
        box-shadow: 0 4px 10px rgba(244, 63, 94, 0.3) !important;
    }

    /* Add Item Button (+ ব্যাজ যোগ করুন / + FAQ যোগ করুন) */
    .section-card .btn-outline-primary,
    .section-card button.btn-outline-primary {
        background: rgba(79, 70, 229, 0.06) !important;
        color: #4F46E5 !important;
        border: 1.5px dashed rgba(79, 70, 229, 0.35) !important;
        border-radius: 10px !important;
        padding: 10px 18px !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        transition: all 0.2s ease !important;
    }

    .section-card .btn-outline-primary:hover {
        background: rgba(79, 70, 229, 0.14) !important;
        border-color: #4F46E5 !important;
        color: #4338CA !important;
        transform: translateY(-1px);
    }

    /* Mobile Mockup Styles */
    .device-container {
        position: sticky;
        top: 30px;
        margin: 0 auto;
        width: 340px;
        height: 670px;
        background: #090D16;
        border-radius: 40px;
        box-shadow: 0 25px 60px -10px rgba(15, 23, 42, 0.3), 0 0 0 12px #0B132B;
        border: 3px solid #1E293B;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        z-index: 10;
    }

    .device-header-notch {
        width: 140px;
        height: 18px;
        background: #1E293B;
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
        z-index: 100;
    }

    .device-screen {
        flex: 1;
        width: 100%;
        height: 100%;
        background: #F8FAFC;
        overflow-y: auto;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
        padding-top: 18px;
        padding-bottom: 60px;
        position: relative;
    }

    .device-screen::-webkit-scrollbar {
        width: 4px;
    }

    .device-screen::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 2px;
    }

    /* Simulated App Style */
    .sim-app-header {
        background: #FFFFFF;
        border-bottom: 1px solid #E2E8F0;
        padding: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .sim-app-header .brand {
        font-weight: 800;
        font-size: 14px;
        color: #4F46E5;
        letter-spacing: -0.02em;
    }

    .sim-app-header i {
        font-size: 14px;
        color: #64748B;
    }

    .sim-product-image {
        width: 100%;
        height: 220px;
        background-color: #E2E8F0;
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
    }

    .sim-product-info-card {
        background: #FFFFFF;
        padding: 14px;
        border-bottom: 1px solid #E2E8F0;
    }

    .sim-product-name {
        font-weight: 700;
        font-size: 15px;
        color: #0F172A;
        margin-bottom: 6px;
        line-height: 1.3;
    }

    .sim-pricing-row {
        display: flex;
        align-items: baseline;
        gap: 6px;
    }

    .sim-current-price {
        font-weight: 800;
        color: #4F46E5;
        font-size: 19px;
    }

    .sim-old-price {
        text-decoration: line-through;
        color: #94A3B8;
        font-size: 13px;
    }

    .sim-discount-badge {
        background: #FEF2F2;
        color: #EF4444;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;
    }

    /* Live Preview Section Styles */
    .sim-section-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 12px;
        margin: 10px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    }

    .sim-section-header {
        text-align: center;
        margin-bottom: 8px;
    }

    .sim-section-tag {
        font-size: 8px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 10px;
        text-transform: uppercase;
        display: inline-block;
        margin-bottom: 2px;
    }

    .sim-section-title {
        font-size: 12px;
        font-weight: 700;
        margin: 0;
    }

    .sim-badge-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }

    .sim-badge-item {
        background: #F8FAFC;
        border: 1px solid #F1F5F9;
        border-radius: 8px;
        padding: 8px 6px;
        text-align: center;
    }

    .sim-badge-item i {
        font-size: 15px;
        margin-bottom: 3px;
    }

    .sim-badge-title {
        font-weight: 700;
        font-size: 10.5px;
        color: #0F172A;
    }

    .sim-badge-desc {
        font-size: 8.5px;
        color: #64748B;
    }

    .sim-buy-button-wrapper {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: #FFFFFF;
        border-top: 1px solid #E2E8F0;
        padding: 10px 14px;
        display: flex;
        justify-content: center;
        z-index: 50;
    }

    .sim-buy-button {
        background: linear-gradient(135deg, #10B981, #059669);
        color: #FFFFFF;
        width: 100%;
        text-align: center;
        padding: 9px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 13.5px;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
    }
</style>
@endpush
