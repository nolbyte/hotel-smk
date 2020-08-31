<?php
defined("RESMI") or die("Akses ditolak");
if ($_SESSION['role'] === '1' || $_SESSION['role'] === '2' || $_SESSION['role'] === '3'){
?>
<ul class="nav navbar-nav">        
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-key"></i> Check In / Out<span class="caret"></span></a>
    <ul class="dropdown-menu" role="menu">
      <li><a href="index.php?mod=transaksi&hal=booking">Check In</a></li>
      <li><a href="index.php?mod=transaksi&hal=checkout">Check Out</a></li>              
      <li><a href="index.php?mod=transaksi&hal=checkin-list">Tamu In-House</a></li>
      <li><a href="index.php?mod=transaksi&hal=checkout-list">Tamu Off-House</a></li>
    </ul>
  </li>
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-book"></i> Room Services<span class="caret"></span></a>
    <ul class="dropdown-menu" role="menu">
      <li><a href="index.php?mod=transaksi&hal=pesan-layanan">Pesan Layanan/Produk</a></li>
      <li><a href="index.php?mod=kamar&hal=kamar-kotor">Pembersihan Kamar</a></li>               
    </ul>
  </li>
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bed"></i> Kamar<span class="caret"></span></a>
    <ul class="dropdown-menu" role="menu">
      <li><a href="index.php?mod=kamar&hal=list"><i class="fa fa-circle-o"></i> Lihat Kamar</a></li>
      <li><a href="index.php?mod=kamar&hal=tambah"><i class="fa fa-circle-o"></i> Tambah Kamar</a></li>        
      <li><a href="index.php?mod=kamar&hal=tipe-list"><i class="fa fa-circle-o"></i> Tipe Kamar</a></li>        
    </ul>
  </li>
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cutlery"></i> Layanan<span class="caret"></span></a>
    <ul class="dropdown-menu" role="menu">
      <li><a href="index.php?mod=layanan&hal=list"><i class="fa fa-circle-o"></i> Lihat Layanan</a></li>
      <li><a href="index.php?mod=layanan&hal=kategori"><i class="fa fa-circle-o"></i> Kategori Layanan</a></li>                        
    </ul>
  </li> 
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-users"></i> Buku Tamu<span class="caret"></span></a>
    <ul class="dropdown-menu" role="menu">
      <li><a href="index.php?mod=tamu&hal=tamu-list"><i class="fa fa-circle-o"></i> Lihat Tamu</a></li>
      <li><a href="index.php?mod=tamu&hal=tamu-add"><i class="fa fa-circle-o"></i> Tambah Tamu Baru</a></li>                        
    </ul>
  </li>  
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-gears"></i> System<span class="caret"></span></a>
    <ul class="dropdown-menu" role="menu">
      <li><a href="index.php?mod=system&hal=hotel"><i class="fa fa-circle-o"></i> Hotel</a></li>
      <li><a href="index.php?mod=system&hal=user"><i class="fa fa-circle-o"></i> User</a></li>                        
    </ul>
  </li>    
</ul>
<?php 
  }
  if($_SESSION['role'] === '4'){
?>
<ul class="nav navbar-nav">        
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-key"></i> Check In / Out<span class="caret"></span></a>
    <ul class="dropdown-menu" role="menu">
      <li><a href="index.php?mod=transaksi&hal=booking">Check In</a></li>
      <li><a href="index.php?mod=transaksi&hal=checkout">Check Out</a></li>      
      <li><a href="index.php?mod=transaksi&hal=checkin-list">Tamu In-House</a></li>
       <li><a href="index.php?mod=transaksi&hal=checkout-list">Tamu Off-House</a></li>
    </ul>
  </li>
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-book"></i> Room Services<span class="caret"></span></a>
    <ul class="dropdown-menu" role="menu">
      <li><a href="index.php?mod=transaksi&hal=pesan-layanan">Pesan Layanan/Produk</a></li>
      <li><a href="index.php?mod=kamar&hal=kamar-kotor">Pembersihan Kamar</a></li>               
    </ul>
  </li>  
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-users"></i> Buku Tamu<span class="caret"></span></a>
    <ul class="dropdown-menu" role="menu">
      <li><a href="index.php?mod=tamu&hal=tamu-list"><i class="fa fa-circle-o"></i> Lihat Tamu</a></li>
      <li><a href="index.php?mod=tamu&hal=tamu-add"><i class="fa fa-circle-o"></i> Tambah Tamu Baru</a></li>                        
    </ul>
  </li>   
</ul>
<?php } ?>