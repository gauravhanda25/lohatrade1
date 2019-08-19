jQuery(document).ready(function($){
	"use strict";

	var $chartSelect = $('.advert-chart');
	var $noData = $('.no-data');
	var $chartPanel = $('#dashboard-chart');
	var $toRemove = $('.to-remove');
	var chart;

	$chartSelect.on('change', function(){
		$.ajax({
			url: adifier_data.ajaxurl,
			method: 'POST',
			data:{
				action: 'chart_data',
				advert_id: $chartSelect.val()
			},
			dataType: 'JSON',
			success: function( response ){
				$toRemove.remove();
				if( response.empty == true ){
					$chartPanel.hide();
					$noData.removeClass('hidden');
				}
				else{
					if( typeof chart !== 'undefined' ){
						chart.destroy();
					}
					$chartPanel.show();
					$noData.addClass('hidden');

					chart = new Chart($chartPanel, {
					    type: 'line',
					    data: {
					        labels: response.labels,
					        datasets: [{
					            data: response.data,
					           	backgroundColor: adifier_data.main_color,
								borderColor: adifier_data.main_color,
								fill: false,
								borderWidth: 2,
								tension: 0
					        }]
					    },
						options: {
							responsive: true,
							legend:{
								display: false
							},							
							tooltips: {
								mode: 'index',
								intersect: false,
							},
							hover: {
								mode: 'nearest',
								intersect: true
							},
							scales: {
								yAxes: [{
									ticks: {
										beginAtZero:true,
										max: response.max < 10 ? response.max + 1 : ( response.max + 3 ) % 2 == 0 ? response.max + 3 : response.max + 2,
										stepSize: response.max < 10 ? 1 : Math.round( ( response.max + 3) / 10 )
									}
								}]
							}
						}
					});
				}
			}
		});
	});

	$(window).load(function(){
		$chartSelect.prop('disabled', false);
	});
});