@push('styles')
<style>
    /* Tab Styles */
    .nav-tabs-solid {
        background-color: #f1f5f9;
        border-radius: 12px;
        padding: 5px;
        border: none;
    }
    .nav-tabs-solid .nav-item {
        margin-bottom: 0;
    }
    .nav-tabs-solid .nav-link {
        border: none;
        border-radius: 8px;
        color: #64748b;
        font-weight: 600;
        padding: 10px 16px;
        transition: all 0.2s ease;
        text-align: center;
    }
    .nav-tabs-solid .nav-link:hover {
        color: #1e293b;
        background-color: rgba(255, 255, 255, 0.5);
    }
    .nav-tabs-solid .nav-link.active {
        background-color: #fff !important;
        color: #0f172a !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }

    /* Mobile Mockup Styles */
    .device-container {
        position: sticky;
        top: 30px;
        margin: 0 auto;
        width: 330px;
        height: 660px;
        background: #090d16;
        border-radius: 36px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 10px #1e293b;
        border: 4px solid #334155;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        z-index: 10;
    }
    .device-header-notch {
        width: 140px;
        height: 18px;
        background: #1e293b;
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
        background: #f8fafc;
        overflow-y: auto;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
        padding-top: 18px; /* Safe area for notch */
        padding-bottom: 60px; /* Safe area for buy button */
        position: relative;
    }
    .device-screen::-webkit-scrollbar {
        width: 4px;
    }
    .device-screen::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 2px;
    }

    /* Simulated App Style */
    .sim-app-header {
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .sim-app-header .brand {
        font-weight: 800;
        font-size: 14px;
        color: #2563eb;
    }
    .sim-app-header i {
        font-size: 14px;
        color: #64748b;
    }
    .sim-product-image {
        width: 100%;
        height: 220px;
        background-color: #e2e8f0;
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
    }
    .sim-product-info-card {
        background: #fff;
        padding: 12px;
        border-bottom: 1px solid #e2e8f0;
    }
    .sim-product-name {
        font-weight: 700;
        font-size: 15px;
        color: #0f172a;
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
        color: #2563eb;
        font-size: 18px;
    }
    .sim-old-price {
        text-decoration: line-through;
        color: #94a3b8;
        font-size: 13px;
    }
    .sim-discount-badge {
        background: #fef2f2;
        color: #ef4444;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;
    }
    .sim-countdown-banner {
        background: linear-gradient(135deg, #ef4444, #b91c1c);
        color: #fff;
        padding: 10px;
        text-align: center;
        border-radius: 8px;
        margin: 10px;
    }
    .sim-countdown-title {
        font-weight: 700;
        font-size: 12px;
        margin-bottom: 2px;
    }
    .sim-countdown-subtitle {
        font-size: 10px;
        opacity: 0.8;
        margin-bottom: 4px;
    }
    .sim-countdown-timer {
        display: flex;
        justify-content: center;
        gap: 6px;
    }
    .sim-timer-box {
        background: rgba(0,0,0,0.2);
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
    }

    /* Live Preview Section Styles */
    .sim-section-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px;
        margin: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
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
    .sim-item-row {
        display: flex;
        align-items: flex-start;
        gap: 6px;
        font-size: 11px;
        color: #334155;
        padding: 4px 0;
    }
    .sim-item-row i {
        margin-top: 2px;
        font-size: 10px;
    }

    .sim-faq-item {
        border-bottom: 1px solid #f1f5f9;
        padding: 6px 0;
    }
    .sim-faq-q {
        font-weight: 700;
        font-size: 11px;
        color: #1e293b;
    }
    .sim-faq-a {
        font-size: 10px;
        color: #64748b;
        margin-top: 2px;
    }

    .sim-badge-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 6px;
    }
    .sim-badge-item {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 6px;
        padding: 6px;
        text-align: center;
    }
    .sim-badge-item i {
        font-size: 14px;
        margin-bottom: 2px;
    }
    .sim-badge-title {
        font-weight: 700;
        font-size: 10px;
        color: #0f172a;
    }
    .sim-badge-desc {
        font-size: 8px;
        color: #64748b;
    }

    .sim-buy-button-wrapper {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: #fff;
        border-top: 1px solid #e2e8f0;
        padding: 8px 12px;
        display: flex;
        justify-content: center;
        z-index: 50;
    }
    .sim-buy-button {
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        width: 100%;
        text-align: center;
        padding: 8px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }
</style>
@endpush
