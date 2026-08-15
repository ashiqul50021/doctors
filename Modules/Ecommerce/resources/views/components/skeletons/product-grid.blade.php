@props(['count' => 6, 'colClass' => 'col-lg-4 col-md-6 col-sm-6 col-6 mb-4'])

@for($i = 0; $i < $count; $i++)
    @include('ecommerce::components.skeletons.product-card', ['colClass' => $colClass])
@endfor
