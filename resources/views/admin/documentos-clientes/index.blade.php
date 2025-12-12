@extends('adminlte::page')

@section('title', 'Documentos de Clientes')

@section('content_header')
    <h1>Documentos de Clientes</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                @can('admin.documentos-clientes.create')
                    <a href="{{ route('admin.documentos-clientes.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Documento
                    </a>
                @endcan
            </div>
            <div>
                <form method="GET" action="{{ route('admin.documentos-clientes.index') }}" class="d-inline-flex gap-2">
                    @if(!auth()->user()->hasRole('Cliente'))
                        <select name="cliente_id" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="">Todos los clientes</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->user->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    <select name="tipo_documento" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="">Todos los tipos</option>
                        <option value="factura" {{ request('tipo_documento') == 'factura' ? 'selected' : '' }}>Factura</option>
                        <option value="contrato" {{ request('tipo_documento') == 'contrato' ? 'selected' : '' }}>Contrato</option>
                        <option value="garantia" {{ request('tipo_documento') == 'garantia' ? 'selected' : '' }}>Garantía</option>
                        <option value="orden_compra" {{ request('tipo_documento') == 'orden_compra' ? 'selected' : '' }}>Orden de Compra</option>
                        <option value="otro" {{ request('tipo_documento') == 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    @if(request('cliente_id') || request('tipo_documento'))
                        <a href="{{ route('admin.documentos-clientes.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    @endif
                </form>
            </div>
        </div>
        <div class="card-body">
            <table id="documentosTable" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Título</th>
                        <th>Cliente</th>
                        <th>Cotización</th>
                        <th>Tipo</th>
                        <th>Número Doc.</th>
                        <th>Fecha Doc.</th>
                        <th>Subido por</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($documentos as $documento)
                        <tr>
                            <td>{{ $documento->id }}</td>
                            <td><strong>{{ $documento->titulo }}</strong></td>
                            <td>{{ $documento->cliente->user->name ?? 'N/A' }}</td>
                            <td>
                                @if($documento->cotizacion)
                                    <a href="{{ route('admin.cotizaciones.show', $documento->cotizacion) }}">
                                        {{ $documento->cotizacion->numero_cotizacion }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $tipos = [
                                        'factura' => ['badge' => 'success', 'text' => 'Factura'],
                                        'contrato' => ['badge' => 'primary', 'text' => 'Contrato'],
                                        'garantia' => ['badge' => 'info', 'text' => 'Garantía'],
                                        'orden_compra' => ['badge' => 'warning', 'text' => 'Orden Compra'],
                                        'otro' => ['badge' => 'secondary', 'text' => 'Otro']
                                    ];
                                    $tipo = $tipos[$documento->tipo_documento] ?? $tipos['otro'];
                                @endphp
                                <span class="badge badge-{{ $tipo['badge'] }}">{{ $tipo['text'] }}</span>
                            </td>
                            <td>{{ $documento->numero_documento ?? 'N/A' }}</td>
                            <td>{{ $documento->fecha_documento ? $documento->fecha_documento->format('d/m/Y') : 'N/A' }}</td>
                            <td>{{ $documento->usuario->name ?? 'N/A' }}</td>
                            <td>
                                @if($documento->activo)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.documentos-clientes.download', $documento->id) }}"
                                       class="btn btn-info btn-sm"
                                       title="Descargar"
                                       target="_blank">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @can('admin.documentos-clientes.show')
                                        <a href="{{ route('admin.documentos-clientes.show', $documento->id) }}"
                                           class="btn btn-secondary btn-sm"
                                           title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endcan
                                    @can('admin.documentos-clientes.edit')
                                        <a href="{{ route('admin.documentos-clientes.edit', $documento->id) }}"
                                           class="btn btn-warning btn-sm"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('admin.documentos-clientes.destroy')
                                        <form action="{{ route('admin.documentos-clientes.destroy', $documento->id) }}"
                                              method="POST"
                                              style="display: inline-block;"
                                              onsubmit="return confirm('¿Está seguro de eliminar este documento? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">No hay documentos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#documentosTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
                },
                "pageLength": 25,
                "order": [[0, "desc"]]
            });
        });
    </script>
@stop

