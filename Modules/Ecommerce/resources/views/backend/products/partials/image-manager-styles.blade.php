@push('styles')
<style>
    .image-manager-shell {
        border: 1px dashed #dbe2ea;
        border-radius: 14px;
        background: #f8fbff;
        padding: 14px;
    }

    .gallery-preview-group + .gallery-preview-group {
        margin-top: 16px;
    }

    .gallery-preview-label {
        display: block;
        margin-bottom: 10px;
        color: #334155;
        font-size: 13px;
        font-weight: 600;
    }

    .single-image-preview img {
        width: 150px;
        height: 150px;
        object-fit: cover;
    }

    .gallery-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 14px;
    }

    .gallery-preview-card {
        border: 1px solid #dbe2ea;
        border-radius: 14px;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        position: relative;
        display: flex;
        flex-direction: column;
        height: 220px;
    }

    .gallery-preview-card img {
        width: 100%;
        height: 140px;
        object-fit: cover;
        display: block;
        background: #f8fafc;
    }

    .gallery-preview-meta {
        padding: 8px 10px 4px;
        color: #64748b;
        font-size: 11px;
        line-height: 1.4;
        word-break: break-all;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .gallery-preview-actions {
        padding: 0 10px 10px;
        margin-top: auto;
    }

    .gallery-add-card {
        border: 2px dashed #cbd5e1;
        border-radius: 14px;
        background: #fff;
        height: 220px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        color: #64748b;
    }

    .gallery-add-card:hover {
        border-color: #3b82f6;
        background: #eff6ff;
        color: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.1);
    }

    .gallery-add-card i {
        font-size: 28px;
        margin-bottom: 8px;
    }

    .gallery-add-card span {
        font-size: 12px;
        font-weight: 600;
    }

    .gallery-empty-note {
        color: #64748b;
        font-size: 12px;
        margin-top: 8px;
    }
</style>
@endpush
