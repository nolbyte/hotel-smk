<?php
defined("RESMI") or die("Akses ditolak");
if(!empty($_GET['id'])){
	$id = intval($_GET['id']);
}
//Ambil Transaksi kamar
$sqlt = $db->prepare("SELECT * FROM transaksi_kamar tk
		JOIN tamu t ON tk.id_tamu=t.id_tamu
		JOIN kamar k ON tk.id_kamar=k.id_kamar
		JOIN kamar_tipe kt ON k.id_kamar_tipe=kt.id_kamar_tipe
		WHERE tk.id_transaksi_kamar = ?");
$sqlt->bindParam(1, $id);
$sqlt->execute();
$trans = $sqlt->fetch(PDO::FETCH_ASSOC);
if($trans === FALSE){
	echo 'Transaksi tidak ditemukan';
	die();
}
//Ambil transaksi layanan
$sqll = $db->prepare("SELECT * FROM transaksi_layanan tl
	JOIN layanan l ON tl.id_layanan=l.id_layanan
	WHERE id_transaksi_kamar = ?");
$sqll->bindParam(1, $id);
$sqll->execute();

//hitung total layanan
$sub = $db->prepare("SELECT sum(total) as totalSub FROM transaksi_layanan WHERE id_transaksi_kamar = ?");
$sub->execute(array($id));
$sb = $sub->fetch(PDO::FETCH_ASSOC);
//hitung transaksi
$checkin=date_create($trans['tanggal_checkin']);
$checkout=date_create($trans['tanggal_checkout']);
$durasi=date_diff($checkin,$checkout)->format('%a');
$subtotal_kamar=$durasi * $trans['harga_malam'];
$subtotal=$subtotal_kamar + $sb['totalSub'];
$ppn=$subtotal * 0.21;
$total=$subtotal + $ppn;
$grand_total=$subtotal + $ppn - $trans['deposit'];
$tglM = date('M d', strtotime($trans['tanggal_checkin']));
$tglC = date('M d', strtotime($trans['tanggal_checkout']));
$hari = date('Y-m-d');
$tangall = '';
if($hari <= $trans['tanggal_checkout']){
	$tanggall = date("Y-m-d", strtotime($trans['tanggal_checkout'].'-1 day'));
}elseif ($hari > $trans['tanggal_checkout']) {
	$tanggall = date("Y-m-d", strtotime($hari.'-1 day'));
}else{
	echo 'nah error';
}
if(isset($_POST['checkout'])){
	$gump = new GUMP();
	$_POST = array(
		'id_kamar'           => $_POST['id_kamar'],
		'id_transaksi_kamar' => $_POST['id_transaksi_kamar']
	);
	$_POST = $gump->sanitize($_POST);
	$gump->validation_rules(array(
		'id_kamar'           => 'required|integer',
		'id_transaksi_kamar' => 'required|integer'
	));
	$gump->filter_rules(array(
		'id_kamar'           => 'trim|sanitize_numbers',
		'id_transaksi_kamar' => 'trim|sanitize_numbers'
	));
	$ok = $gump->run($_POST);
	if($ok === false){
		$error[] = $gump->get_readable_errors(true);
	}else{
		$status = 'CHECK OUT';
		$statusK = 'KOTOR';
		$sql = $db->prepare("UPDATE transaksi_kamar SET status = ? WHERE id_transaksi_kamar = ?");
		$sql->bindParam(1, $status);
		$sql->bindParam(2, $_POST['id_transaksi_kamar']);
		$sqlk = $db->prepare("UPDATE kamar SET status_kamar = ? WHERE id_kamar = ?");
		$sqlk->bindParam(1, $statusK);
		$sqlk->bindParam(2, $_POST['id_kamar']);

		if(!$sql->execute()){
			print_r($sql->errorInfo());
		}elseif(!$sqlk->execute()){
			print_r($sqlk->errorInfo());
		}else{
			$_SESSION['SaveData']= '';
			echo "<script> location.replace('index.php?mod=transaksi&hal=checkout'); </script>";
		}
	}
}
?>
<div class="row">
	<?php
	if(isset($error)){
		foreach($error as $error){
			?>
			<script>toastr.error('<?php echo $error ?>', 'Error!', {timeOut: 3000, progressBar: true})</script>
			<?php
		}
	}
	?>
	<div class="box box-success">
		<div class="box-header with-border">
			<h3 class="box-title">Check Out</h3>&nbsp;&nbsp;<small>proses checkout tamu</small>
		</div>
		<form method="post" action="">
			<div class="box-body">				
				<h3>NOMOR KAMAR: <?= $trans['nomor_kamar']?></h3>
				<div class="row">
					<div class="col-sm-3">						
						<div class="alert alert-info">
							<h4><?= $trans['nama_kamar_tipe']; ?></h4>
							<ul class="list-unstyled">
								<li>Harga / Malam : <b><?= format_rp($trans['harga_malam']); ?></b></li>
								<li>Maximal Orang Dewasa : <b><?= $trans['max_dewasa']; ?> Orang</b></li>
								<li>Maximal Anak-anak : <b><?= $trans['max_anak']; ?> Orang</b></li>
							</ul>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label class="control-label">#INVOICE</label>
							<input type="text" name="nomor_invoice" class="form-control" value="<?= $trans['nomor_invoice']?>" readonly>
						</div>
						<div class="form-group">
							<label class="control-label">Nama Tamu</label>
							<input class="form-control" value="<?= $trans['prefix'].' '.$trans['nama_depan'].' '.$trans['nama_belakang']?>" readonly>							
						</div>										
					</div>
					<div class="col-sm-5">
						<div class="form-group">
							<label>Jumlah Tamu</label>
							<div class="row">
								<div class="col-sm-6">
									<input class="form-control" value="<?= $trans['jumlah_dewasa'].' Orang Dewasa'; ?>" readonly>									
								</div>
								<div class="col-sm-6">
									<input class="form-control" value="<?= $trans['jumlah_anak'].' Anak-anak'; ?>" readonly>									
								</div>
							</div>
						</div>
						<div class="form-group">
							<label>Tanggal / Waktu Check-In</label>
							<div class="row">
								<div class="col-sm-6">
									<input id="checkin" class="form-control" name="tanggal_checkin" data-date-format="yyyy-mm-dd" value="<?= $trans['tanggal_checkin']; ?>" readonly>
								</div>
								<div class="col-sm-6">
									<input class="form-control" name="waktu_checkin" value="<?= $trans['waktu_checkin']; ?>" readonly>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label>Tanggal / Waktu Check-Out</label>
							<div class="row">
								<div class="col-sm-6">
									<input id="checkout" class="form-control" name="tanggal_checkout" data-date-format="yyyy-mm-dd" value="<?= $trans['tanggal_checkout']; ?>">
								</div>
								<div class="col-sm-6">
									<input class="form-control" name="waktu_checkout" value="<?= $trans['waktu_checkout']; ?>">
								</div>
							</div>
						</div>						
					</div>
				</div>
				<h3>Rincian Tagihan</h3>
				<hr>
				<table class="table table-bordered">
					<tr>
						<td></td>
						<td class="text-center">DATE</td>
						<td class="text-center">REFERENCE</td>
						<td class="text-center">DESCRIPTION</td>
						<td class="text-center">AMOUNT (Rp.)</td>
					</tr>
					<tr>
						<td></td>
						<td><?= $tglM?></td>
						<td></td>
						<td>Posting Adv Depos</td>
						<td class="text-right"><?= number_format($trans['deposit'])?><span class="pull-right">&nbsp;&nbsp;-</span></td>
					</tr>
					<?php
						$data = createRange($trans['tanggal_checkin'], $tanggall, 'M d');
						implode(',', $data);
						$sum1 = 0;
						foreach($data as $data){
					?>
					<tr>
						<td></td>
						<td><?= $data;?></td>
						<td>IDR: <?= number_format($trans['harga_malam'])?></td>
						<td>Room Package/<?=$trans['nomor_kamar'].'/'.$trans['nama_depan']?></td>
						<td class="text-right"><?= number_format($trans['harga_malam'])?><span class="pull-right">&nbsp;&nbsp;+</span></td>
					</tr>
					<?php
						$sum1 += $trans['harga_malam'];
						} 
						$no = 2;
						$no2 = $no+1;
						$sum2 = 0;
						foreach($sqll->fetchAll() as $row){ 
						$tgll = date('M d', strtotime($row['tanggal']));
					?>
						<tr>
							<td></td>
							<td><?= $tgll ?></td>
							<td>IDR: <?= number_format($row['harga_layanan'])?></td>
							<td><?= $row['jumlah'].'&nbsp;'.$row['satuan'].'&nbsp;'.$row['nama_layanan']?></td>
							<td class="text-right"><?= number_format($row['total'])?><span class="pull-right">&nbsp;&nbsp;+</span></td>
						</tr>
					<?php 
						$sum2 += $row['total'];
						} 
					?>
					<tr>
						<td colspan="5"></td>
					</tr>
					<tr>
						<td colspan="4" class="text-right">Sub Total</td>
						<td class="text-right">
							<?php
								$subT = $sum1+$sum2;
							 	echo number_format($subT);
							 ?>
						</td>
					</tr>
					<tr>
						<td colspan="4" class="text-right">Tax(21%)</td>
						<td class="text-right">
							<?php
								$tax = $subT*0.21;
							 	echo number_format($tax);
							?>
						</td>
					</tr>
					<tr>
						<td colspan="4" class="text-right">Total</td>
						<td class="text-right">
							<?= number_format($subT+$tax)?>
						</td>
					</tr>
					<tr>
						<td colspan="4" class="text-right">Payment Due (Deposit - Total)</td>
						<td class="text-right">
							<?= number_format($subT+$tax-$trans['deposit'])?>
						</td>
					</tr>
				</table>
			</div>
			<div class="box-footer">
				<input type="hidden" name="id_kamar" value="<?= $trans['id_kamar']; ?>">
				<input type="hidden" name="id_transaksi_kamar" value="<?= $trans['id_transaksi_kamar']; ?>">
				<input type="hidden" name="jumlah_pembayaran" value="<?= $grand_total; ?>">
				<button class="btn btn-success" type="submit" name="checkout">Check Out</button>
				<a class="btn btn-primary" href="invoice.php?id=<?= $trans['id_transaksi_kamar']; ?>" target="_blank">Cetak Invoice</a>
				<a class="btn btn-warning" href="index.php?mod=transaksi&hal=checkin-list">Batal</a>
			</div>
		</form>
	</div>
</div>