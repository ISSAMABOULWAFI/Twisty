<script>
var base_url="{$base_url}";
var contr_link="{$contr_link}";
var item_not_found_msg="Non Trouvé!";
var nb_changes=0;
var max_changes=3;
var is_db_occuped=false;
var history_saved=[];
</script>

<style>
.container{
	/*background-color:red;*/
	width:100%;
}
#listtwisty_table{
	width:100%;
}
.left-row-side{
	width:80%;
	background-color:white;
}
.right-row-side{
	width:20%;
	/*background-color:yellow;*/
}
.grid-container {
  display: grid;
  grid-gap: 5px;
  grid-template-columns: 100px 100px 100px 100px 100px 100px 100px 100px;
  /*background-color: #2196F3;*/
  padding: 5px;
}
.grid-item {
  /*background-color: rgba(255, 255, 255, 0.8);*/
  border: 1px solid rgba(0, 0, 0, 0.8);
  /*border: 2px solid #000000;*/
  border-radius: 7px;
  padding: 20px;
  font-size: 30px;
  color:#000000;
  text-align: center;
  
  position: relative;
    display: inline-block;
	
	
	-webkit-box-shadow: 5px 5px 5px 0px rgba(0,0,0,0.75);
	-moz-box-shadow:    5px 5px 5px 0px rgba(0,0,0,0.75);
	box-shadow:         5px 5px 5px 0px rgba(0,0,0,0.75);
}

.is_hover {
  height: 130%;
  width: 130%;
  z-index:500;
  margin-left:-15px;
  margin-top:-15px;
}

.num-commande{
	font-size: 10px;
	position: absolute;
    bottom: 0;
    right: 0;
	padding:2px;
}
.grid-item .reset{
	position: absolute;
    top: 0;
    left: 0;
	padding:2px;
	cursor: pointer;
	/*background-color:red;*/
	background-image: url("{$base_url}imgs/reset-icon.png");
	background-size: 15px 15px;
	margin-top:5px;
	margin-left:5px;
	width:15px;
	height:15px;
}
.grid-item .outofstock{
	/*background-color:#D3D8DB;*/
	background-image: url("{$base_url}imgs/outofstock.png");
	background-size: 15px 15px;
	cursor: pointer;
	width:15px;
	height:15px;
	font-size: 10px;
	position: absolute;
    top: 0;
    right: 0;
	margin-top: 5px;
    margin-right: 5px;
	
}
.info-box{
	font-size: 80px;
	/*background-color: gray;*/
	height:200px;
	
	text-align:center;
	
	color:#000000;
}
.info-box>.inner{
	position: relative;
	top: 50%;
	transform: translateY(-50%);
}
.items-list{
	background-color:red;
	position: absolute;
    top: 70px;
    left: 0px;
	z-index:500;
	display:none;
	opacity: 0.9;
}
.items-list ul li {
	font-size: 15px;
}
.info-error{
	font-size: 19px;
	text-align:center;
	color:#C70039;
	font-weight: bold;
	position: relative;
	top: -30px;
}
#right-box{
	border: 2px solid #000000;
    border-radius: 50px 20px;
}
.page-head{
	display:none;
}
#content{
	padding:10px 10px 0;
}
.panel-title{
	padding-top:8px;
}
.detail-row{
	font-size:10px;
	
}
table.items-list {
	background-color:#C7C7C8;
	font-size:10px;
	border: 2px solid #8A8A8B;
    
}


table.items-list td:nth-child(2){
	
	min-width:300px;
}

.tablehead{
	font-weight:bold;
	background-color:#939394;
}
</style>

<style>
#debug-bar{
	background-color:#EFF1F2;
	border: 1px solid #D3D8DB;
	height:30px;
	position:relative;
}
#debug-bar .loading-img{
	position:absolute;
	right:0px;
	top:0px;
	display:none;
}
#debug-bar .loading-img img{
	height:28px;
}
#debug-bar .msg{
	padding:5px;
}
#msg-bar{
	display:none;
}
.left-side{
	text-align:left;
}
.finished{
	background-color:#B7E1CD;
}
.inprog{
	background-color:#FCE8B2;
}
.blnk{
	background-color:#F4C7C3;
}
.myinfobulle{
	font-size:10px;
	background-color:#42381F;
	color:#DEA21F;
	position:absolute;
	left:20px;
	top:-10px;
	width:190px;
	display:none;
	border: 1px solid #000000;
    border-radius: 10px;
	padding:10px;
	z-index:504;
}
#myloading{
	display:none;
	background-color:#2C3234;
	opacity:.9;
	width:100%;
	height:700px;
	position:absolute;
	top:0px;
	left:0px;
	border-radius: 5px;
	z-index:599;
	text-align:center;
}
#myloading img{
	margin-top:100px;
	width:100px;
	height:100px;
	opacity:.9;
}

</style>
<script src="{$base_url}js/jspdf.min.js"></script>
<script src="{$base_url}js/jspdf.plugin.autotable.min.js"></script>

<div class="panel">
	<div id="myloading">
		<img src="{$base_url}imgs/loading1.gif" />
	</div>
	<div class="panel-heading">
		<h1 class="panel-title">Twisty</h1>
		<span class="panel-heading-action">
			<!--<a id="desc-carrier-new" class="list-toolbar-btn" href="index.php?controller=AdminCarriers&amp;token=db6d7e20bd3126c2dc3be6727119c49d&amp;onboarding_carrier">
				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Ajouter" data-html="true" data-placement="top">
					<i class="process-icon-new"></i>
				</span>
			</a>-->
			<a class="list-toolbar-btn" href="javascript:twistylist.populateTwistyTableInDb()">
				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Rafraîchir la liste" data-html="true" data-placement="bottom">
					<i class="process-icon-refresh"></i>
				</span>
			</a>
									
			<a class="list-toolbar-btn" href="javascript:download_pdf();">
				<span class="label-tooltip" data-toggle="tooltip" data-original-title="Télécharger PDF" data-html="true" data-placement="bottom">
					<i class="icon-print"></i>
				</span>
			</a>
			
			<!--<a class="list-toolbar-btn" href="javascript:twistylist.removeAllTwistyOrdersRows();" >
				<span class="label-tooltip" data-toggle="tooltip" data-original-title="Supprimer toutes les commandes TWISTY" data-html="true" data-placement="bottom">
					<i class="icon-remove-circle"></i>
				</span>
			</a>-->
			
			<a class="list-toolbar-btn" href="javascript:removefinishedorders();" >
				<span class="label-tooltip" data-toggle="tooltip" data-original-title="Libérer les adresses terminées" data-html="true" data-placement="bottom">
					<i class="icon-remove-circle"></i>
				</span>
			</a>
			
			<a class="list-toolbar-btn" href="javascript:settooutofstock();" >
				<span class="label-tooltip" data-toggle="tooltip" data-original-title="Libérer les adresses non terminées" data-html="true" data-placement="bottom">
					<i class="icon-remove-sign"></i>
				</span>
			</a>
			
		</span>
	</div>
	<div class="form-group" id="msg-bar">
		<div id="debug-bar">
			<div class="msg">-</div>
			<div class="loading-img"><img src="{$base_url}imgs/loading.gif"/></div>
		</div>
	</div>
	<div class="form-group">
		<form id="formean" name="EANIN">
			<label class="sr-only" for="exampleInput">EAN CODE</label>
			<input id="ine" type="text" class="form-control" placeholder="EAN" onchange="" name="inputean"   />

		</form>
  
	</div>
	<div class="form-group">
		<!--<button class="btn btn-success" onclick='Javascript:init()'>Importer les derniers commandes</button>
		<button class="btn btn-success" onclick='Javascript:t()'>listtwisty</button>-->
		<!--<button class="btn btn-success" id="removetwisty">Supprimer tout sans terminer</button>-->
	</div>
	
	<div class="container" style="width:100%;">
		<table id="listtwisty_table">
			<tr>
				<td class="left-row-side" valign="top">
					<div class="grid-container">
						<!--<div class="grid-item" attr-order="">
							<span class="boxname">1</span>
							<span class="num-commande">123456789</span>
							<div class="items-list">
								<ul >
									<li>EAN: 12364548976121564</li>
								</ul>
							</div>
						</div>
						<div class="grid-item">2<span class="num-commande">123456789</span></div>
						<div class="grid-item">3</div>  
						<div class="grid-item">4</div>
						<div class="grid-item">5</div>
						<div class="grid-item">6</div>  
						<div class="grid-item">7</div>
						<div class="grid-item">8</div>
						<div class="grid-item">9</div>  -->
					</div>
				</td>
				<td class="right-row-side" valign="top">
					<div id="right-box">
						
						<div class="info-box">
							
							<div class="inner">__</div>
							
							<div class="info-error">Article non trouvé!</div>
						</div>
					</div>
				</td>
			</tr>
		</table>
		

		
		<table id="basic-table" style="display: none;">
		  <thead>
			<tr>
			  <th class="col1">ID <br/>ORDER</th>
			  <th class="col2">Address <br/>Box</th>
			  <th class="col3">Moyen de <br/>paiement</th>
			  <th class="col4prime">Total (Transport Inclu)</th>
			  <th class="col4">Tarif <br/>Transport</th>
			  <th class="col5">Nombre <br/>d'articles</th>
			  <th class="col6">Qté total <br/>d'articles</th>
			  <th class="col7"></th>
			  <th class="col8">Commentaire</th>
			</tr>
		  </thead>
		  <tbody>
			<tr>
			  <td align="right">1</td>
			  <td>Donna</td>
			  <td>Moore</td>
			  <td>dmoore0@furl.net</td>
			  <td>China</td>
			  <td>211.56.242.221</td>
			  <td><img src="{$base_url}imgs/carreau.png" /></td>
			  <td>211.56.242.221</td>
			</tr>
		  </tbody>
		</table>
	</div>
</div>

