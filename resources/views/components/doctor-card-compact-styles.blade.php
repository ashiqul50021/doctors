<style>
    /* Compact doctor card (copied from the home page design) */
    .doctor-card-new {
        background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        overflow: hidden; transition: all 0.3s ease; height: 100%;
        display: flex; flex-direction: column; border: 1px solid #eef2f6;
    }
    .doctor-card-new:hover { transform: translateY(-5px); box-shadow: 0 12px 35px rgba(0, 102, 255, 0.12); border-color: #1D4ED8; }
    .doctor-card-new .doctor-img-wrapper {
        position: relative; height: 195px; overflow: hidden;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    }
    .doctor-card-new .doctor-img { width: 100%; height: 100%; object-fit: cover; object-position: top center; }
    .doctor-card-new .doctor-fee-badge {
        position: absolute; top: 10px; left: 10px; z-index: 2;
        background: linear-gradient(135deg, #1D4ED8, #3B82F6); color: #fff;
        padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 11px;
        box-shadow: 0 2px 8px rgba(0, 102, 255, 0.25);
    }
    .doctor-card-new .fav-btn {
        position: absolute; top: 10px; right: 10px; z-index: 2;
        width: 30px; height: 30px; border-radius: 50%; background: rgba(255, 255, 255, .9);
        display: inline-flex; align-items: center; justify-content: center;
        color: #64748b; font-size: 13px; opacity: 1; visibility: visible;
    }
    .doctor-card-new .fav-btn.active { color: #1D4ED8; }
    .doctor-card-new .doctor-info { padding: 10px; flex: 1; display: flex; flex-direction: column; }
    .doctor-card-new .doctor-speciality {
        font-size: 10px; color: #1D4ED8; text-transform: uppercase; letter-spacing: .5px;
        margin-bottom: 4px; font-weight: 600; display: inline-block; background: #e8f4ff;
        padding: 3px 8px; border-radius: 4px; width: fit-content; max-width: 100%;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .doctor-card-new .doctor-name { font-size: 13px; font-weight: 700; margin-bottom: 4px; line-height: 1.3; }
    .doctor-card-new .doctor-name a { color: #272b41; text-decoration: none; }
    .doctor-card-new .verified-badge { color: #1D4ED8; font-size: 12px; margin-left: 3px; }
    .doctor-card-new .doctor-rating { display: flex; align-items: center; gap: 4px; margin-bottom: 4px; font-size: 11px; }
    .doctor-card-new .doctor-rating i { color: #ffc107; font-size: 11px; }
    .doctor-card-new .doctor-rating .rating-count { color: #888; font-size: 11px; }
    .doctor-card-new .doctor-location { display: flex; align-items: center; gap: 6px; margin-bottom: 10px; font-size: 11px; color: #666; }
    .doctor-card-new .doctor-location i { color: #1D4ED8; }
    .doctor-card-new .doctor-buttons { display: flex; gap: 6px; margin-top: auto; }
    .doctor-card-new .btn-view-details {
        flex: 1; padding: 6px 4px; background: transparent; border: 1.5px solid #1D4ED8; border-radius: 4px;
        color: #1D4ED8; font-weight: 600; font-size: 11px; text-align: center; text-decoration: none;
    }
    .doctor-card-new .btn-book-appointment {
        flex: 1; padding: 6px 4px; background: linear-gradient(135deg, #1D4ED8, #60A5FA); border: none;
        border-radius: 4px; color: #fff; font-weight: 600; font-size: 11px; text-align: center; text-decoration: none;
    }

    @media (max-width: 767.98px) {
        .doctor-card-new .doctor-img-wrapper { height: 130px; }
        .doctor-card-new .doctor-fee-badge { top: 6px; left: 6px; padding: 2px 6px; font-size: 9px; }
        .doctor-card-new .fav-btn { top: 6px; right: 6px; width: 26px; height: 26px; font-size: 11px; }
        .doctor-card-new .doctor-info { padding: 8px; }
        .doctor-card-new .doctor-speciality { font-size: 9px; padding: 2px 5px; margin-bottom: 2px; }
        .doctor-card-new .doctor-name { font-size: 11.5px; margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .doctor-card-new .doctor-rating { font-size: 10px; margin-bottom: 2px; }
        .doctor-card-new .doctor-rating i { font-size: 10px; }
        .doctor-card-new .doctor-location { font-size: 10px; margin-bottom: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .doctor-card-new .doctor-buttons { flex-direction: column; gap: 4px; }
        .doctor-card-new .btn-view-details,
        .doctor-card-new .btn-book-appointment {
            padding: 4px; font-size: 10px; height: 26px; line-height: 1.4;
            display: flex; align-items: center; justify-content: center;
        }
    }

    /* designation / chamber / location lines on the short doctor card */
    .doctor-card-new .doctor-desig { font-size: 11px; font-weight: 600; color: #1e3a8a; line-height: 1.3; margin-bottom: 4px;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .doctor-card-new .doctor-place { display: flex; align-items: center; gap: 6px; margin-bottom: 3px; font-size: 11px; color: #475569;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .doctor-card-new .doctor-place span { overflow: hidden; text-overflow: ellipsis; }
    .doctor-card-new .doctor-place i { color: #1D4ED8; width: 11px; text-align: center; flex-shrink: 0; }
    .doctor-card-new .doctor-card-rating { margin: 4px 0 8px; }
    .doctor-card-new .doctor-card-rating i.empty { color: #cbd5e1; }
    @media (max-width: 767.98px) {
        .doctor-card-new .doctor-desig { font-size: 10px; margin-bottom: 3px; }
        .doctor-card-new .doctor-place { font-size: 10px; margin-bottom: 2px; }
        .doctor-card-new .doctor-card-rating { margin: 3px 0 6px; }
    }
</style>
