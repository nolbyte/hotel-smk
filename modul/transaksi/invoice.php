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
$sql = $db->prepare("SELECT * FROM transaksi_layanan tl
	JOIN layanan l ON tl.id_layanan=l.id_layanan
	WHERE id_transaksi_kamar = ?");
$sql->bindParam(1, $id);
$sql->execute();

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
$ppn=$subtotal * 0.10;
$total=$subtotal + $ppn;
$grand_total=$subtotal + $ppn - $trans['deposit'];
?>
<div class="row">
	<table class="table">
		<tr>
			<td style="padding-left: 15px">
				Ditujukan Kepada :
				<address>
					<strong><?= $trans['prefix'].'. '.$trans['nama_depan'].'&nbsp;'.$trans['nama_belakang']; ?></strong><br/>
					<?= $trans['alamat_jalan']; ?><br/>
					<?= $trans['alamat_kabupaten'].' - '.$trans['alamat_provinsi']; ?><br/>
					<br/>
					Nomor Telp : <?= $trans['nomor_telp']; ?><br/>
				</address>
			</td>
			<td>
				<b>NOMOR INVOICE : </b><br/>
				<span class="lead"><?= $trans['nomor_invoice']; ?></span><br/><br/>
				<b>Tanggal Terbit :</b><br/>
				<span class="lead"><?= date('d M Y'); ?></span>
			</td>
		</tr>
	</table>
</div>

<h3>RINCIAN TAGIHAN</h3>
<table class="table table-bordered table-striped table-responsive">
	<thead>
		<tr>
			<th>Produk / Layanan</th>
			<th>Harga</th>
			<th>Qty</th>
			<th>Total</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Room Type : <?= $trans['nama_kamar_tipe']; ?></td>
			<td>Rp <?= format_rp($trans['harga_malam']); ?></td>
			<td><?= $durasi; ?> Malam</td>
			<td>Rp <?= format_rp($trans['total_biaya_kamar']); ?></td>
		</tr>
		<?php foreach($sql->fetchAll() as $transaksi_layanan) { ?>
		<tr>
			<td><?= $transaksi_layanan['nama_layanan']; ?></td>
			<td>Rp <?= format_rp($transaksi_layanan['harga_layanan']); ?></td>
			<td><?= $transaksi_layanan['jumlah'].'&nbsp;'.$transaksi_layanan['satuan']; ?></td>
			<td>Rp <?= format_rp($transaksi_layanan['total']); ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<div class="row">
	<table class="table">
		<tr>
			<td style="width: 45%;padding-left: 15px">
				<p class="text-muted well well-sm no-shadow">
					<b>Catatan :</b> Mohon simpan bukti pembayaran ini sebaik mungkin. Pihak hotel tidak akan melayani keluhan-keluhan tamu yang tidak memiliki bukti pembayaran resmi oleh Pihak Hotel
				</p>
			</td>
			<td style="width: 55%">
				<table class="table table-bordered table-responsive">
					<tr>
						<td><b>Sub-Total</b></td>
						<td>Rp <?= format_rp($subtotal); ?></td>
					</tr>
					<tr>
						<td>PPn 10%</td>
						<td>Rp <?= format_rp($ppn); ?></td>
					</tr>					
					<tr>							
						<td>Jumlah Deposit</td>
						<td class="text-red">Rp <?= format_rp($trans['deposit']); ?></td>
					</tr>
					<tr>
						<td><b>Grand Total</b></td>
						<td><b>Rp <?= format_rp($grand_total); ?></b></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
<div class="row">
	<table class="table">
		<tr>
			<td>
				<p style="text-align: center">Front Office</p><br><br><br>
				<p style="text-align: center;font-weight: bold"><?= $_SESSION['nama']?></p>
			</td>
			<td>
				<p style="text-align: center">Customer</p><br><br><br>
				<p style="text-align: center;font-weight: bold"><?= $trans['prefix'].'. '.$trans['nama_depan'].'&nbsp;'.$trans['nama_belakang']; ?></p>
			</td>
		</tr>
	</table>
</div>