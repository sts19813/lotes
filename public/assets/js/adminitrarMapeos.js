
// ============================================================
		//  MODAL ADMINISTRAR LOTES MAPEADOS
		// ============================================================


		$('#btnManageMappings').on('click', function () {
			const tbody = $('#mappedLotsTable');
			tbody.empty();

			const lotsById = Object.fromEntries(
				window.preloadedLots.map(lot => [lot.id, lot.name])
			);
			debugger

			if (!window.dbLotes || window.dbLotes.length === 0) {
				tbody.append(`
						<tr>
							<td colspan="4" class="text-center text-muted">
								No hay lotes mapeados
							</td>
						</tr>
					`);
			} else {
				window.dbLotes.forEach(item => {

					const lotName = lotsById[item.lote_id] ?? '—';
					debugger

					tbody.append(`
							<tr>
								<td><code>${item.selectorSVG}</code></td>
								<td>${lotName}</td>
								<td>
									<button 
										class="btn btn-sm btn-danger btn-delete-mapping"
										data-lot="${item.id}">
										<i class="fas fa-trash"></i>
									</button>
								</td>
							</tr>
						`);
				});
			}
			$('#mappedLotsModal').modal('show');
		});


		$('#mappedLotsTable').on('click', '.btn-delete-mapping', function () {
			const lotId = $(this).data('lot');

			Swal.fire({
				title: '¿Eliminar mapeo?',
				text: 'El polígono quedará libre nuevamente',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#d33',
				confirmButtonText: 'Sí, eliminar'
			}).then(result => {
				if (!result.isConfirmed) return;
				$.ajax({
					url: '/api/polygon-lot',
					type: 'DELETE',
					data: {
						lot_id: lotId,
						_token: window.Laravel.csrfToken
					},
					success: () => {

						Swal.fire({
							icon: 'success',
							title: 'Mapeo eliminado',
							timer: 1200,
							showConfirmButton: false
						}).then(() => {
							// 🔄 Recarga total de la página
							location.reload();
						});
					}
				});
			});
		});