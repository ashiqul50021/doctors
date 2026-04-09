@php
    $variantRows = collect($variantRows ?? [])->values();

    if ($variantRows->isEmpty()) {
        $variantRows = collect([[
            'id' => '',
            'option_name' => '',
            'option_value' => '',
            'price' => '',
            'sale_price' => '',
            'stock' => '',
            'sku' => '',
            'is_active' => true,
        ]]);
    }
@endphp

<div class="col-12">
    <div class="card border mt-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Product Variants</h5>
                    <p class="text-muted mb-0">Optional. Add purchasable options like size, strength, or pack size.</p>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addVariantRowBtn">
                    <i class="fe fe-plus"></i> Add Variant
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="min-width: 130px;">Type</th>
                            <th style="min-width: 160px;">Value</th>
                            <th style="min-width: 120px;">Price</th>
                            <th style="min-width: 120px;">Sale Price</th>
                            <th style="min-width: 110px;">Stock</th>
                            <th style="min-width: 140px;">SKU</th>
                            <th style="width: 90px;">Active</th>
                            <th style="width: 90px;">Remove</th>
                        </tr>
                    </thead>
                    <tbody id="variantRows">
                        @foreach($variantRows as $index => $variant)
                            <tr class="variant-row">
                                <td>
                                    <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant['id'] ?? '' }}">
                                    <input type="text"
                                        name="variants[{{ $index }}][option_name]"
                                        class="form-control"
                                        placeholder="Size, Strength"
                                        value="{{ $variant['option_name'] ?? '' }}">
                                </td>
                                <td>
                                    <input type="text"
                                        name="variants[{{ $index }}][option_value]"
                                        class="form-control"
                                        placeholder="500mg, XL"
                                        value="{{ $variant['option_value'] ?? '' }}">
                                </td>
                                <td>
                                    <input type="number"
                                        step="0.01"
                                        min="0"
                                        name="variants[{{ $index }}][price]"
                                        class="form-control"
                                        value="{{ $variant['price'] ?? '' }}">
                                </td>
                                <td>
                                    <input type="number"
                                        step="0.01"
                                        min="0"
                                        name="variants[{{ $index }}][sale_price]"
                                        class="form-control"
                                        value="{{ $variant['sale_price'] ?? '' }}">
                                </td>
                                <td>
                                    <input type="number"
                                        min="0"
                                        name="variants[{{ $index }}][stock]"
                                        class="form-control"
                                        value="{{ $variant['stock'] ?? '' }}">
                                </td>
                                <td>
                                    <input type="text"
                                        name="variants[{{ $index }}][sku]"
                                        class="form-control"
                                        placeholder="Optional SKU"
                                        value="{{ $variant['sku'] ?? '' }}">
                                </td>
                                <td class="text-center">
                                    <input type="checkbox"
                                        name="variants[{{ $index }}][is_active]"
                                        value="1"
                                        {{ !array_key_exists('is_active', $variant) || $variant['is_active'] ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger js-remove-variant-row">
                                        <i class="fe fe-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <small class="text-muted d-block mt-3">
                Main product price and stock above are for simple products. Active variants will control storefront stock when present.
            </small>
        </div>
    </div>
</div>

<template id="variantRowTemplate">
    <tr class="variant-row">
        <td>
            <input type="hidden" name="variants[__INDEX__][id]" value="">
            <input type="text"
                name="variants[__INDEX__][option_name]"
                class="form-control"
                placeholder="Size, Strength"
                value="">
        </td>
        <td>
            <input type="text"
                name="variants[__INDEX__][option_value]"
                class="form-control"
                placeholder="500mg, XL"
                value="">
        </td>
        <td>
            <input type="number"
                step="0.01"
                min="0"
                name="variants[__INDEX__][price]"
                class="form-control"
                value="">
        </td>
        <td>
            <input type="number"
                step="0.01"
                min="0"
                name="variants[__INDEX__][sale_price]"
                class="form-control"
                value="">
        </td>
        <td>
            <input type="number"
                min="0"
                name="variants[__INDEX__][stock]"
                class="form-control"
                value="">
        </td>
        <td>
            <input type="text"
                name="variants[__INDEX__][sku]"
                class="form-control"
                placeholder="Optional SKU"
                value="">
        </td>
        <td class="text-center">
            <input type="checkbox" name="variants[__INDEX__][is_active]" value="1" checked>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger js-remove-variant-row">
                <i class="fe fe-trash"></i>
            </button>
        </td>
    </tr>
</template>
