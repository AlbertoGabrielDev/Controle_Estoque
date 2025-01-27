<form action="{{ route(request()->segment(2) . '.index') }}" method="GET" class="mb-3">
    <div class="input-group">
        <input type="text" class="form-control mt-2" name="searchLike" value="{{ request('searchLike') }}" placeholder="Buscar...">
        <button class="btn btn-primary" type="submit">Buscar</button>
    </div>
</form>