<style>
    /* Skeleton Shimmer Animation Base */
    .skeleton-shimmer {
        background: #e2e8f0;
        background-image: linear-gradient(
            90deg,
            rgba(255, 255, 255, 0) 0,
            rgba(255, 255, 255, 0.45) 20%,
            rgba(255, 255, 255, 0.7) 60%,
            rgba(255, 255, 255, 0)
        );
        background-size: 200% 100%;
        background-repeat: no-repeat;
        animation: skeletonShimmer 1.5s infinite linear;
        border-radius: 6px;
    }

    @keyframes skeletonShimmer {
        0% {
            background-position: -200% 0;
        }
        100% {
            background-position: 200% 0;
        }
    }

    /* Product Card Skeleton Structure */
    .product-card-skeleton {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
        position: relative;
    }

    .skeleton-img-wrap {
        width: 100%;
        height: 180px;
        background-color: #f1f5f9;
        position: relative;
        overflow: hidden;
    }

    .skeleton-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        width: 65px;
        height: 18px;
        border-radius: 10px;
        z-index: 2;
    }

    .skeleton-details {
        padding: 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .skeleton-rating-row {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 8px;
    }

    .skeleton-rating-stars {
        width: 60px;
        height: 12px;
    }

    .skeleton-rating-text {
        width: 35px;
        height: 12px;
    }

    .skeleton-brand {
        width: 45%;
        height: 12px;
        margin-bottom: 10px;
    }

    .skeleton-title-1 {
        width: 90%;
        height: 15px;
        margin-bottom: 6px;
    }

    .skeleton-title-2 {
        width: 65%;
        height: 15px;
        margin-bottom: 16px;
    }

    .skeleton-footer {
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding-top: 4px;
    }

    .skeleton-price-block {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .skeleton-price-main {
        width: 60px;
        height: 18px;
    }

    .skeleton-price-sub {
        width: 40px;
        height: 12px;
    }

    .skeleton-btn-group {
        display: flex;
        gap: 6px;
    }

    .skeleton-btn-cart {
        width: 38px;
        height: 38px;
        border-radius: 8px;
    }

    .skeleton-btn-buy {
        width: 60px;
        height: 38px;
        border-radius: 8px;
    }

    /* Doctor Card Skeleton Structure */
    .doctor-card-skeleton {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
        position: relative;
    }

    .skeleton-doc-img-wrap {
        width: 100%;
        height: 200px;
        background-color: #f1f5f9;
        position: relative;
        overflow: hidden;
    }

    .skeleton-doc-fee {
        position: absolute;
        top: 12px;
        left: 12px;
        width: 55px;
        height: 22px;
        border-radius: 6px;
        z-index: 2;
    }

    .skeleton-doc-fav {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        z-index: 2;
    }

    .skeleton-doc-info {
        padding: 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .skeleton-doc-speciality {
        width: 40%;
        height: 12px;
        margin-bottom: 8px;
    }

    .skeleton-doc-name {
        width: 75%;
        height: 16px;
        margin-bottom: 8px;
    }

    .skeleton-doc-rating {
        width: 55%;
        height: 12px;
        margin-bottom: 8px;
    }

    .skeleton-doc-location {
        width: 65%;
        height: 12px;
        margin-bottom: 16px;
    }

    .skeleton-doc-buttons {
        margin-top: auto;
        display: flex;
        gap: 8px;
    }

    .skeleton-doc-btn {
        flex: 1;
        height: 38px;
        border-radius: 8px;
    }

    /* Hero Banner Skeleton Structure */
    .hero-banner-skeleton {
        width: 100%;
        min-height: 380px;
        background: #ffffff;
        border-radius: 20px;
        padding: 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 30px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        margin-bottom: 25px;
    }

    .skeleton-hero-left {
        flex: 1;
        max-width: 550px;
    }

    .skeleton-hero-title-1 {
        width: 85%;
        height: 36px;
        border-radius: 8px;
        margin-bottom: 12px;
    }

    .skeleton-hero-title-2 {
        width: 65%;
        height: 36px;
        border-radius: 8px;
        margin-bottom: 16px;
    }

    .skeleton-hero-sub {
        width: 75%;
        height: 18px;
        border-radius: 6px;
        margin-bottom: 24px;
    }

    .skeleton-hero-badge {
        width: 160px;
        height: 24px;
        border-radius: 12px;
        margin-bottom: 24px;
    }

    .skeleton-hero-btn {
        width: 180px;
        height: 48px;
        border-radius: 10px;
    }

    .skeleton-hero-right {
        width: 380px;
        height: 300px;
        border-radius: 16px;
    }

    @media (max-width: 991px) {
        .hero-banner-skeleton {
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }
        .skeleton-hero-left {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .skeleton-hero-right {
            width: 100%;
            height: 220px;
        }
    }

    @media (max-width: 575px) {
        .skeleton-img-wrap {
            height: 140px;
        }
        .skeleton-doc-img-wrap {
            height: 160px;
        }
        .skeleton-details,
        .skeleton-doc-info {
            padding: 12px;
        }
        .skeleton-hero-title-1,
        .skeleton-hero-title-2 {
            height: 26px;
        }
    }
</style>
