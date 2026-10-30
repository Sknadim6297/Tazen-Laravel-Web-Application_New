@php $omit = $omit ?? []; @endphp
@if ($filters['q'] !== '' && !in_array('q', $omit, true))
    <input type="hidden" name="q" value="{{ $filters['q'] }}">
@endif
@if ($filters['category'] !== '' && !in_array('category', $omit, true))
    <input type="hidden" name="category" value="{{ $filters['category'] }}">
@endif
@if ($filters['type'] !== 'all' && !in_array('type', $omit, true))
    <input type="hidden" name="type" value="{{ $filters['type'] }}">
@endif
@if ($filters['sort'] !== 'relevant' && !in_array('sort', $omit, true))
    <input type="hidden" name="sort" value="{{ $filters['sort'] }}">
@endif
@if ($filters['view'] !== 'grid' && !in_array('view', $omit, true))
    <input type="hidden" name="view" value="{{ $filters['view'] }}">
@endif
@if ($filters['service'] !== '' && !in_array('service', $omit, true))
    <input type="hidden" name="service" value="{{ $filters['service'] }}">
@endif
@if ($filters['sub_service'] !== '' && !in_array('sub_service', $omit, true))
    <input type="hidden" name="sub_service" value="{{ $filters['sub_service'] }}">
@endif
@if (!in_array('types', $omit, true))
    @foreach ($filters['types'] as $type)
        <input type="hidden" name="types[]" value="{{ $type }}">
    @endforeach
@endif
@if (!in_array('price', $omit, true))
    @foreach ($filters['price'] as $price)
        <input type="hidden" name="price[]" value="{{ $price }}">
    @endforeach
@endif
@if (!in_array('rating', $omit, true))
    @foreach ($filters['rating'] as $rating)
        <input type="hidden" name="rating[]" value="{{ $rating }}">
    @endforeach
@endif
@if (!in_array('availability', $omit, true))
    @foreach ($filters['availability'] as $availability)
        <input type="hidden" name="availability[]" value="{{ $availability }}">
    @endforeach
@endif
