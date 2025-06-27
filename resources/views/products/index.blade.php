@extends('layouts.dashboard')

@section('content')
    <div class="">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Products</h4>
            </div>
            <div class="card-body">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">Add Product</button>
                <table class="table mt-3 table-hovered" id="datatables">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Satuan</th>
                            {{-- <th>Quantity</th> --}}
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $product->code }}</td>
                                <td><img src="{{ asset($product->image) }}" alt="" width="100px"></td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->satuan }}</td>
                                {{-- <td>{{ $product->quantity }}</td> --}}
                                <td>{{ formatRupiah($product->price) }}</td>
                                <td class="d-flex">
                                    <button class="btn btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $product->id }}">Edit</button>
                                    <button class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $product->id }}">Delete</button>
                                </td>
                            </tr>

                            <div class="modal fade" id="editModal{{ $product->id }}" tabindex="-1"
                                aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('products.update', $product->id) }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="row">
                                                    <div class="col-md-6">

                                                        <div class="mb-3">
                                                            <label for="code" class="form-label">Code</label>
                                                            <input type="text" class="form-control" id="code"
                                                                name="code" value="{{ $product->code }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="name" class="form-label">Name</label>
                                                            <input type="text" class="form-control" id="name"
                                                                name="name" value="{{ $product->name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="satuan" class="form-label">Satuan</label>
                                                            <select name="satuan" id="satuan" class="form-control">
                                                                <option value="pcs"
                                                                    {{ $product->satuan == 'pcs' ? 'selected' : '' }}>Pcs
                                                                </option>
                                                                <option value="botol"
                                                                    {{ $product->satuan == 'botol' ? 'selected' : '' }}>
                                                                    Botol
                                                                </option>
                                                                <option value="set"
                                                                    {{ $product->satuan == 'set' ? 'selected' : '' }}>Set
                                                                </option>
                                                            </select>
                                                        </div>
                                                        {{-- <div class="mb-3">
                                                            <label for="quantity" class="form-label">Quantity</label>
                                                            <input type="number" class="form-control" id="quantity"
                                                                name="quantity" value="{{ $product->quantity }}" required>
                                                        </div> --}}
                                                    </div>
                                                    <div class="col-md-6">

                                                        <div class="mb-3">
                                                            <label for="price" class="form-label">Price</label>
                                                            <input type="text" class="form-control" id="price"
                                                                name="price" value="{{ formatRupiah($product->price) }}"
                                                                required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="description" class="form-label">Description</label>
                                                            <textarea class="form-control" id="description" name="description">{{ $product->description }}</textarea>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="image" class="form-label">Product Image</label>
                                                            <input type="file" class="form-control" id="image"
                                                                name="image" onchange="previewImage(event)">

                                                            <div class="mt-2">
                                                                <img id="imagePreview" src="{{ asset($product->image) }}"
                                                                    alt="Image preview" width="100"
                                                                    class="img-thumbnail">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="submit"
                                                    class="btn btn-primary mt-3 float-end">Update</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $product->id }}" tabindex="-1"
                                aria-labelledby="deleteModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to delete this product?
                                        </div>
                                        <div class="modal-footer">
                                            <form action="{{ route('products.destroy', $product->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                            </form>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Create Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">

                                <div class="mb-3">
                                    <label for="code" class="form-label">Code</label>
                                    <input type="text" class="form-control" id="code" name="code" required>
                                </div>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="satuan" class="form-label">Satuan</label>
                                    <select class="form-select" id="satuan" name="satuan" required>
                                        <option value="pcs">Pcs</option>
                                        <option value="botol">Botol</option>
                                        <option value="set">Set</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price</label>
                                    <input type="text" class="form-control" id="price" name="price" required>
                                </div>

                                {{-- <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" required>
                                </div> --}}
                                <div class="mb-3">
                                    <label for="image" class="form-label">Product Image</label>
                                    <input type="file" class="form-control" id="image" name="image"
                                        onchange="previewImage(event)">
                                </div>
                                <div class="mt-2">
                                    <img id="imagePreview" src="#" alt="Image preview" width="100"
                                        class="img-thumbnail d-none">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3 float-end">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).on('input', '#price', function() {
            var value = $(this).val().replace(/[^0-9]/g, '');
            $(this).val('Rp ' + value.replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
        });

        $('#image').on('change', function(event) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(event.target.files[0]);
        });

        function previewImage(event) {
            const image = event.target.files[0];
            const preview = document.getElementById('imagePreview');
            if (image) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                };
                reader.readAsDataURL(image);
            } else {
                preview.src = '#';
                preview.classList.add('d-none');
            }
        }
    </script>
@endsection
