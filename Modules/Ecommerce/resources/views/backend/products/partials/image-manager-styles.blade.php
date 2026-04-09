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
    }

    .gallery-preview-card img {
        width: 100%;
        height: 145px;
        object-fit: cover;
        display: block;
        background: #f8fafc;
    }

    .gallery-preview-meta {
        padding: 10px 12px 8px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
        word-break: break-word;
    }

    .gallery-preview-actions {
        padding: 0 12px 12px;
    }

    .gallery-empty-note {
        color: #64748b;
        font-size: 12px;
        margin-top: 8px;
    }
</style>
@endpush
