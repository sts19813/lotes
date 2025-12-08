@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1 class="fw-bold text-gray-800">Lotes</h1>

        <div class="d-flex gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalLot">
                <i class="ki-duotone ki-plus fs-2"></i> Nuevo Lote
            </button>

            <button id="btnDownloadTemplate" class="btn btn-success">
                <i class="ki-duotone ki-download fs-2"></i> Descargar Plantilla
            </button>

            <input type="file" id="inputImport" accept=".xlsx" hidden>
            <button id="btnImport" class="btn btn-info">
                <i class="ki-duotone ki-upload fs-2"></i> Importar Lotes
            </button>
        </div>
    </div>
    <!--  Filtros arriba -->
    <div class="row mb-5">
        <div class="col-md-4">
            <select id="filterProject" class="form-select">
                <option value="">Todos los proyectos</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <select id="filterPhase" class="form-select" disabled>
                <option value="">Selecciona un proyecto...</option>
            </select>
        </div>
        <div class="col-md-4">
            <select id="filterStage" class="form-select" disabled>
                <option value="">Selecciona una fase...</option>
            </select>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="lotsTable" class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Proyecto</th>
                        <th>Fase</th>
                        <th>Etapa</th>
                        <th>Depth</th>
                        <th>Front</th>
                        <th>Area</th>
                        <th>Precio/m²</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Chepina</th>
                        <th>Creado</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- modal Editar Lote -->
    <div class="modal fade" id="modalEditLot" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-600px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Lote</h5>
                    <button class="btn btn-sm btn-icon" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-2"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="formEditLot">
                        @csrf

                        <input type="hidden" id="editLotId" name="id">

                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input id="editLotName" name="name" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Depth</label>
                            <input id="editLotDepth" name="depth" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Front</label>
                            <input id="editLotFront" name="front" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Area</label>
                            <input id="editLotArea" name="area" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Precio m²</label>
                            <input id="editLotPriceM2" name="price_square_meter" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Total</label>
                            <input id="editLotTotal" name="total_price" class="form-control">
                        </div>
                        <div class="text-end">
                            <button class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>



    <!-- Modal Crear Lote -->
    <div class="modal fade" id="modalLot" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-600px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Lote</h5>
                    <button class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-2"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formLot">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Proyecto</label>
                            <select id="lotProject" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fase</label>
                            <select id="lotPhase" name="phase_id" class="form-select" required disabled>
                                <option value="">Selecciona primero un proyecto...</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Etapa</label>
                            <select id="lotStage" name="stage_id" class="form-select" required disabled>
                                <option value="">Selecciona primero una fase...</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Depth</label>
                            <input type="number" step="0.01" name="depth" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Front</label>
                            <input type="number" step="0.01" name="front" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Area</label>
                            <input type="number" step="0.01" name="area" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Precio / m²</label>
                            <input type="number" step="0.01" name="price_square_meter" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total</label>
                            <input type="number" step="0.01" name="total_price" class="form-control">
                        </div>
                        @php
                            $statusMap = [
                                'for_sale' => 'Disponible',
                                'sold' => 'Vendido',
                                'reserved' => 'Apartado',
                                'locked_sale' => 'Bloqueado',
                            ];
                        @endphp
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Selecciona un status</option>

                                @foreach($statusMap as $value => $label)
                                    <option value="{{ $value }}" @if(isset($financiamiento) && $financiamiento->status === $value)
                                    selected @endif>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Chepina</label>
                            <input type="text" name="chepina" class="form-control">
                        </div>


                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Guardar Lote</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="{{ asset('assets/js/admin/catalogoNabooLotes.js') }}"></script>
@endpush