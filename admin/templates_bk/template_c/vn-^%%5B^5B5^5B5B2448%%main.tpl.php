<?php /* Smarty version 2.6.30, created on 2026-02-05 10:52:34
         compiled from main/main.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'main/main.tpl', 54, false),)), $this); ?>
<div class="contentmain">
	<div class="main">
		<div class="left_sidebar padding10">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "left.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
		<div class="right_content">
			<div class="wrap-tk">
				<div class="box-ana">
					<a class="ana-item" href="index.php?do=articlelist&comp=2">
						<i class="fa-solid fa-newspaper"></i>
						<div class="ana-item-info">
							<span><?php echo $this->_tpl_vars['total_products_count']; ?>
</span>
							<label>Tổng sản phẩm</label>
						</div>
					</a>
					<a class="ana-item" href="index.php?do=articlelist&comp=1">
						<i class="fa-solid fa-pen-to-square"></i>
						<div class="ana-item-info">
							<span><?php echo $this->_tpl_vars['total_news_count']; ?>
</span>
							<label>Tổng bài viết</label>
						</div>
					</a>
					<a class="ana-item" href="index.php?do=orders">
						<i class="fa-solid fa-cart-arrow-down"></i>
						<div class="ana-item-info">
							<span><?php echo $this->_tpl_vars['total_order_count']; ?>
</span>
							<label>Đơn hàng</label>
						</div>
					</a>
					<a class="ana-item" href="index.php?do=contact&comp=23">
						<i class="fa-solid fa-address-book"></i>
						<div class="ana-item-info">
							<span><?php echo $this->_tpl_vars['total_contact_count']; ?>
</span>
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
							<?php $_from = $this->_tpl_vars['browser_counts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['browser'] => $this->_tpl_vars['count']):
?>
							browserLabels.push("<?php echo ((is_array($_tmp=$this->_tpl_vars['browser'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
");
							browserData.push(<?php echo $this->_tpl_vars['count']; ?>
);
							<?php endforeach; endif; unset($_from); ?>
						</script>
						
						
						<?php echo '
							<script>
							var ctx = document.getElementById(\'browserChart\');

							var chart = new Chart(ctx, {
								type: \'doughnut\',
								data: {
								labels: browserLabels,
								datasets: [{
									data: browserData,
									backgroundColor: [
									\'#4285F4\',
									\'#FF7139\',
									\'#ff0000\',
									\'#34A853\',
									\'#999999\',
									\'#f76080\'
									],
									borderWidth: 0
								}]
								},
								options: {
								cutout: \'65%\',
								plugins: {
									legend: {
									display: false   // 👈 TẮT legend mặc định
									}
								}
								}
							});

							// ====== TẠO LEGEND HTML ======
							var legend = document.getElementById(\'browserLegend\');
							var total = browserData.reduce(function(a, b) { return a + b; }, 0);

							browserLabels.forEach(function(label, i) {
								var value = browserData[i];
								var percent = ((value / total) * 100).toFixed(1);

								var div = document.createElement(\'div\');
								div.className = \'item\';

								// div.innerHTML =
								// \'<span class="color" style="background:\' + chart.data.datasets[0].backgroundColor[i] + \'"></span>\' +
								// \'<strong>\' + label + \'</strong>&nbsp;:&nbsp;\' + value + \' (\' + percent + \'%)\';
								div.innerHTML =
								\'<span class="color" style="background:\' + chart.data.datasets[0].backgroundColor[i] + \'"></span>\'
								 + label + \'<span class="card-num" style="color:\'+chart.data.datasets[0].backgroundColor[i]+\'">\' + value + \'</span>\';

								legend.appendChild(div);
							});
							</script>
						'; ?>


					
					</div>

					<div class="box-browers">
						<h2>📈 Thống kê truy cập</h2>
						<div class="stats">
							<div class="card"><strong>Đang online</strong>
								<span id="online"><?php echo $this->_tpl_vars['online_visits']; ?>
<span>
							</div>
							<div class="card"><strong>Trong tuần</strong>
								<span id="week"><?php echo $this->_tpl_vars['week_visits']; ?>
<span>
							</div>
							<div class="card"><strong>Trong tháng</strong>
								<span id="month"><?php echo $this->_tpl_vars['month_visits']; ?>
<span>
							</div>
							<div class="card"><strong>Tổng truy cập</strong>
								<span id="total"><?php echo $this->_tpl_vars['total_visits']; ?>
<span>
							</div>
						</div>
					</div>
					<div class="box-browers scroll">
						<h2>📈 Thống kê truy cập theo vùng</h2>

						<div class="box-browers__tk">
							<?php $_from = $this->_tpl_vars['region_stats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['row']):
?>
							<div class="tk-item">
								<div class="tk-item__ttl"><?php echo $this->_tpl_vars['row']['region']; ?>
</div>
								<div class="tk-item__total"><?php echo $this->_tpl_vars['row']['total']; ?>
 lượt</div>
							</div>
							<?php endforeach; endif; unset($_from); ?>
						</div>
					</div>
				</div>
				<div class="box-browers width-100 mrg-15">
					<h2>📊 Top 20 link truy cập nhiều nhất theo tháng – <?php echo $this->_tpl_vars['year']; ?>
</h2>

					<!-- TAB HEADER -->
					<ul class="month-tabs">
						<?php $_from = $this->_tpl_vars['topByMonth']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['month'] => $this->_tpl_vars['links']):
?>
						<li class="<?php if ($this->_tpl_vars['month'] == date ( 'n' )): ?>active<?php endif; ?>" data-tab="month<?php echo $this->_tpl_vars['month']; ?>
">
							Tháng <?php echo $this->_tpl_vars['month']; ?>

						</li>
						<?php endforeach; endif; unset($_from); ?>
					</ul>

					<!-- TAB CONTENT -->
					<?php $_from = $this->_tpl_vars['topByMonth']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['month'] => $this->_tpl_vars['links']):
?>
					<div class="tab-content <?php if ($this->_tpl_vars['month'] == date ( 'n' )): ?>active<?php endif; ?>" id="month<?php echo $this->_tpl_vars['month']; ?>
">
						<table class="br1">
							<thead>
								<tr>
									<th>Thứ tự</th>
									<th>Link</th>
									<th>Lượt truy cập</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($this->_tpl_vars['links']): ?>
									<?php $_from = $this->_tpl_vars['links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i'] => $this->_tpl_vars['row']):
?>
									<tr>
										<td align="center"><?php echo $this->_tpl_vars['i']+1; ?>
</td>
										<td><a href="<?php echo $this->_tpl_vars['row']['url']; ?>
" target="_blank"><?php echo $this->_tpl_vars['row']['url']; ?>
</a></td>
										<td align="center">
											<span class="badge"><?php echo $this->_tpl_vars['row']['total']; ?>
</span>
										</td>
									</tr>
									<?php endforeach; endif; unset($_from); ?>
								<?php else: ?>
									<tr>
										<td colspan="3" align="center">Không có dữ liệu</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
					<?php endforeach; endif; unset($_from); ?>

					<canvas id="monthChart" height="90"></canvas>
					<script>
						const labels = [
							"Tháng 1","Tháng 2","Tháng 3","Tháng 4","Tháng 5","Tháng 6",
							"Tháng 7","Tháng 8","Tháng 9","Tháng 10","Tháng 11","Tháng 12"
						];

						const data = <?php echo $this->_tpl_vars['months_json']; ?>
;
						</script>

						<?php echo '
						<script>
						const monthCtx = document.getElementById(\'monthChart\').getContext(\'2d\');

						new Chart(monthCtx, {
							type: \'bar\',
							data: {
								labels: labels,
								datasets: [{
									label: \'Lượt truy cập\',
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
						'; ?>


				</div>

			</div>
		</div>
	</div>
</div>