<div class="contentmain">
	<div class="main">
		<div class="left_sidebar padding10">
			{include file="left.tpl"}
		</div>
		<div class="right_content">
			<div class="wrap-tk">
				<div class="box-ana">
					<a class="ana-item" href="index.php?do=articlelist&comp=2">
						<i class="fa-solid fa-newspaper"></i>
						<div class="ana-item-info">
							<span>{$total_products_count}</span>
							<label>Tổng sản phẩm</label>
						</div>
					</a>
					<a class="ana-item" href="index.php?do=articlelist&comp=1">
						<i class="fa-solid fa-pen-to-square"></i>
						<div class="ana-item-info">
							<span>{$total_news_count}</span>
							<label>Tổng bài viết</label>
						</div>
					</a>
					<a class="ana-item" href="index.php?do=orders">
						<i class="fa-solid fa-cart-arrow-down"></i>
						<div class="ana-item-info">
							<span>{$total_order_count}</span>
							<label>Đơn hàng</label>
						</div>
					</a>
					<a class="ana-item" href="index.php?do=contact&comp=23">
						<i class="fa-solid fa-address-book"></i>
						<div class="ana-item-info">
							<span>{$total_contact_count}</span>
							<label>Liên hệ</label>
						</div>
					</a>
				</div>
				<div class="wrap-analytic">
					<div class="box-browers">
						<h2 class="box-ttl2">📈 Thống kê trình duyệt truy cập</h2>

						<div class="browser-flex">
							<div class="chart-wrap">
								<canvas id="browserChart"></canvas>
							</div>
							<div class="browser-legend" id="browserLegend"></div>
						</div>
						
					
						<script>
							const browserLabels = [];
							const browserData = [];
							{foreach from = $browser_counts key = browser item = count}
							browserLabels.push("{$browser|escape:'javascript'}");
							browserData.push({
								$count
							});
							{/foreach}
						</script>
						
						
						{literal}
							<script>
							var ctx = document.getElementById('browserChart');

							var chart = new Chart(ctx, {
								type: 'doughnut',
								data: {
								labels: browserLabels,
								datasets: [{
									data: browserData,
									backgroundColor: [
									'#4285F4',
									'#FF7139',
									'#ff0000',
									'#34A853',
									'#999999',
									'#f76080'
									],
									borderWidth: 0
								}]
								},
								options: {
								cutout: '65%',
								plugins: {
									legend: {
									display: false   // 👈 TẮT legend mặc định
									}
								}
								}
							});

							// ====== TẠO LEGEND HTML ======
							var legend = document.getElementById('browserLegend');
							var total = browserData.reduce(function(a, b) { return a + b; }, 0);

							browserLabels.forEach(function(label, i) {
								var value = browserData[i];
								var percent = ((value / total) * 100).toFixed(1);

								var div = document.createElement('div');
								div.className = 'item';

								// div.innerHTML =
								// '<span class="color" style="background:' + chart.data.datasets[0].backgroundColor[i] + '"></span>' +
								// '<strong>' + label + '</strong>&nbsp;:&nbsp;' + value + ' (' + percent + '%)';
								div.innerHTML =
								'<span class="color" style="background:' + chart.data.datasets[0].backgroundColor[i] + '"></span>'
								 + label + '<span class="card-num" style="color:'+chart.data.datasets[0].backgroundColor[i]+'">' + value + '</span>';

								legend.appendChild(div);
							});
							</script>
						{/literal}

					
					</div>

					<div class="box-browers">
						<h2>📈 Thống kê truy cập</h2>
						<div class="stats">
							<div class="card"><strong>Đang online</strong>
								<span id="online">{$online_visits}<span>
							</div>
							<div class="card"><strong>Trong tuần</strong>
								<span id="week">{$week_visits}<span>
							</div>
							<div class="card"><strong>Trong tháng</strong>
								<span id="month">{$month_visits}<span>
							</div>
							<div class="card"><strong>Tổng truy cập</strong>
								<span id="total">{$total_visits}<span>
							</div>
						</div>
					</div>
					<div class="box-browers scroll">
						<h2>📈 Thống kê truy cập theo vùng</h2>

						<div class="box-browers__tk">
							{foreach from=$region_stats item=row}
							<div class="tk-item">
								<div class="tk-item__ttl">{$row.region}</div>
								<div class="tk-item__total">{$row.total} lượt</div>
							</div>
							{/foreach}
						</div>
					</div>
				</div>
				<div class="box-browers width-100 mrg-15">
					<h2>📊 Top 20 link truy cập nhiều nhất theo tháng – {$year}</h2>

					<!-- TAB HEADER -->
					<ul class="month-tabs">
						{foreach from=$topByMonth key=month item=links}
						<li class="{if $month == date('n')}active{/if}" data-tab="month{$month}">
							Tháng {$month}
						</li>
						{/foreach}
					</ul>

					<!-- TAB CONTENT -->
					{foreach from=$topByMonth key=month item=links}
					<div class="tab-content {if $month == date('n')}active{/if}" id="month{$month}">
						<table class="br1">
							<thead>
								<tr>
									<th>Thứ tự</th>
									<th>Link</th>
									<th>Lượt truy cập</th>
								</tr>
							</thead>
							<tbody>
								{if $links}
									{foreach from=$links key=i item=row}
									<tr>
										<td align="center">{$i+1}</td>
										<td><a href="{$row.url}" target="_blank">{$row.url}</a></td>
										<td align="center">
											<span class="badge">{$row.total}</span>
										</td>
									</tr>
									{/foreach}
								{else}
									<tr>
										<td colspan="3" align="center">Không có dữ liệu</td>
									</tr>
								{/if}
							</tbody>
						</table>
					</div>
					{/foreach}

					<canvas id="monthChart" height="90"></canvas>
					<script>
						const labels = [
							"Tháng 1","Tháng 2","Tháng 3","Tháng 4","Tháng 5","Tháng 6",
							"Tháng 7","Tháng 8","Tháng 9","Tháng 10","Tháng 11","Tháng 12"
						];

						const data = {$months_json};
						</script>

						{literal}
						<script>
						const monthCtx = document.getElementById('monthChart').getContext('2d');

						new Chart(monthCtx, {
							type: 'bar',
							data: {
								labels: labels,
								datasets: [{
									label: 'Lượt truy cập',
									data: data,
									borderWidth: 1
								}]
							},
							options: {
								responsive: true,
								scales: {
									y: {
										beginAtZero: true
									}
								}
							}
						});
						</script>
						{/literal}

				</div>

			</div>
		</div>
	</div>
</div>