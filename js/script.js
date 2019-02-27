//Global vars
var loadingimg=null;
var loadingbox=null;
var msg=null;
var twistylist=null;
//
var new_search=true;
var new_search_compteur=null;
//
var boxNameElement=null;	
//
var compteur=null;
// Colors
var finishColor="#B7E1CD";
var inProgColor="#FCE8B2";
var beginColor="#F4C7C3";
var clignoteColor="#3AE2CE";
var nonet='#noconnection';
//classes

/*
var MyCompteur = function(f,delay){
	var compteur=null;
	var func=null;
	var dely=null;
	
	this.construct = function(f,delay){
		func=f;
		dely=delay;
	}
	
	this.stop = function(){
		console.log('stop');
		if(compteur!=null)
			clearInterval(compteur);
	}
	
	this.start = function(){
		console.log('start');
		compteur=setInterval(this.func, 5000);
	}
	
	
	this.reset = function(){
		console.log('reset');
		this.stop();
		this.start();
	}
	
	this.construct(f,delay);
}
*/
var BoxNameDiv = function(){
	var msgBox=null;
	var idBox=null;
	
	
	this.construct = function(){
		var el=$('#listtwisty_table .right-row-side');
		idBox=$(el).find('.info-box  .inner');
		msgBox=$(el).find('.info-error');

		this.empty();
	}
	this.setId = function(id){
		if(idBox!=null)
			idBox.text(id);
	}
	this.setErrorMsg = function(msg){
		if(msgBox!=null)
			msgBox.text(msg);
	}
	this.empty = function(){
		if(msgBox!=null)
			msgBox.text('');
		if(idBox!=null)
			idBox.text('--');
	}
	this.hideIdBox = function(){
		$(idBox).css('visibility','hidden');
	}
	this.showIdBox = function(){
		$(idBox).css("visibility",'visible');
	}
	this.hideMsgBox = function(){
		$(msgBox).hide();
	}
	this.showMsgBox = function(){
		$(msgBox).show();
	}
	this.setStyle = function(style){
		$(idBox).css(style);
	}
	this.clignote = function(mainColor,secondColor,found){
		//var mainColor='#FFFFFF';
		//var secondColor='#3AE2CE';
		
		console.log("clignote");
		var timesRun=0;
		var backgroundInterval = setInterval(function () {
			$(msgBox).parent().parent().css("background-color", function () {
				this.switch = !this.switch
				return this.switch ? secondColor : ""
			});
			timesRun++;
			if(timesRun === 7){
				if(found)
					$(msgBox).parent().parent().css("background-color",secondColor);
				else
					$(msgBox).parent().parent().css("background-color",mainColor);
				clearInterval(backgroundInterval);
			}
		}, 100);
	}
	
	this.construct();
}
var TwistyOrder = function(options){
	var root=this;
	var vars = {
		id_order : -1,
		total_qte_picked : 0,
		is_finished : 0,
		id_box : '',
		total_product_quantity : 0,
		total_product_type : 0,
		payment : '',
		total_shipping : 0,
		total_paid_tax_incl : 0
	};
	
	this.construct = function(options){
		$.extend(vars,options);
	}
	
	this.getTotal_shipping = function(){
		return vars.total_shipping;
	}
	this.getPayment = function(){
		return vars.payment;
	}
	this.getTotal_product_type = function(){
		return vars.total_product_type;
	}
	this.getTotal_product_quantity = function(){
		return vars.total_product_quantity;
	}
	this.getId_box = function(){
		return vars.id_box;
	}
	this.getTotal_qte_picked = function(){
		return vars.total_qte_picked;
	}
	this.getTotal_paid_tax_incl = function(){
		return vars.total_paid_tax_incl;
	}
	
	this.getStatuCode = function(){
		if(vars.is_finished==1){
			return 2;
		}else if(vars.total_qte_picked < vars.total_product_quantity && vars.total_qte_picked>0){
			return 1;
		}else if(vars.total_qte_picked < vars.total_product_quantity && vars.total_qte_picked==0){
			return 0;
		}
	}
	
	this.ChangeStatToPicked = function(){
		//console.log('ChangeStatToPicked');
		$.ajax({
			url: contr_link+"&action=changeorderstate&id_order="+vars.id_order,
			beforeSend: function( xhr ) {
				
			}
		})
		.done(function( data ) {
			
			console.log(data);
		})
		.fail(function(xhr, textStatus, errorThrown) {
			console.log(xhr.responseText);
		}).always(function(){
			
		});
	}
	
	this.construct(options);
}
var TwistyOrderRow = function(options){
	var root=this;
	this.vars = {
		id_twisty : '-1',
		qte_picked : 0,
		is_finished : 0,
		id_box : '',
		product_ean13 : 0,
		product_quantity : 0,
		id_order : -1,
		product_name : '',
		payment : '',
		total_shipping : 0,
		product_reference : ''
	};
	this.construct = function(options){
		$.extend(this.vars,options);
	}
	
	this.show = function(){
		console.log(JSON.stringify(this.vars));
	}
	this.construct(options);
};
	
var TwistyList = function(){
	var list=new Array();
	var history=new Array();
	var root= this;
	var is_db_occuped=false;
	
	
	this.getOrdersIds = function(){
		//console.log(list.length);
		var res=new Array();
		list.forEach(function(orderRow){
			res.push(orderRow.vars.id_order);
		});
		
		return $.unique(res);
		
	}
	this.getFinishedOrdersIds = function(){
		//console.log(list.length);
		var res=this.getOrdersIds();
		
		list.forEach(function(orderRow){
			if(orderRow.vars.product_quantity>orderRow.vars.qte_picked){
				//res.push(orderRow.vars.id_order);
				res = res.filter(function(elem){
				   return elem != orderRow.vars.id_order; 
				});
			}
		});
		
		res.sort();
		return $.unique(res);
		
	}
	this.getNotFinishedOrdersIds = function(){
		//console.log(list.length);
		var res=this.getOrdersIds();
		var finishedOrders=this.getFinishedOrdersIds();
		
		
		finishedOrders.forEach(function(id){
			if($.inArray(id,res)){
				//res.push(orderRow.vars.id_order);
				res = res.filter(function(elem){
				   return elem != id; 
				});
			}
		});
		
		res.sort();
		return $.unique(res);
		
	}
	this.getFinishedTwistyIds = function(){
		//console.log(list.length);
		var ordersIds = this.getFinishedOrdersIds();
		
		var res=new Array();
		list.forEach(function(orderRow){
			if($.inArray(orderRow.vars.id_order,ordersIds)>-1)
				res.push(orderRow.vars.id_twisty);
		});
		
		return $.unique(res);
		
	}
	
	this.getOrdersInfos = function(id){
		
		var options=new Object();
		
		list.forEach(function(orderRow){
			//console.log(orderRow);
			if(orderRow.vars.id_order==id){
				if(!options.hasOwnProperty('id_order'))
					options.id_order=orderRow.vars.id_order;
				if(!options.hasOwnProperty('id_box'))
					options.id_box=orderRow.vars.id_box;
				if(!options.hasOwnProperty('payment'))
					options.payment=orderRow.vars.payment;
				if(!options.hasOwnProperty('total_shipping'))
					options.total_shipping=orderRow.vars.total_shipping;
				if(!options.hasOwnProperty('is_finished'))
					options.is_finished=orderRow.vars.is_finished;
				
				if(!options.hasOwnProperty('total_qte_picked')){
					options.total_qte_picked=orderRow.vars.qte_picked;
				}else{
					options.total_qte_picked=parseInt(options.total_qte_picked)+parseInt(orderRow.vars.qte_picked);
				}
				
				if(!options.hasOwnProperty('total_product_quantity')){
					options.total_product_quantity=orderRow.vars.product_quantity;
				}else{
					options.total_product_quantity=parseInt(options.total_product_quantity)+parseInt(orderRow.vars.product_quantity);
				}
				
				if(!options.hasOwnProperty('total_product_type')){
					options.total_product_type=1;
				}else{
					options.total_product_type=parseInt(options.total_product_type)+1;
				}
				
				if(!options.hasOwnProperty('total_paid_tax_incl'))
					options.total_paid_tax_incl=orderRow.vars.total_paid_tax_incl;
				
				
			}
		});
		if(options.total_product_quantity==options.total_qte_picked)
			options.is_finished=1;
		var order=new TwistyOrder(options);
		return order;
	}
	
	this.refreshFromDb = function(){

		list=new Array();
		
		
		$.ajax({
			url: contr_link+"&action=gettwistylist",
			beforeSend: function( xhr ) {
				$(loadingbox).show();
				$(msg).text('Récupération des commandes TWISTY de la BD');
			}
		})
		.done(function( data ) {
			//$('#listtwisty_table').html(data);
			//alert(data);
			var tmp = jQuery.parseJSON( data );
			tmp.forEach(function(item){
				var obj=new TwistyOrderRow(item);
				list.push(obj);
				
				
			});
			
			$(msg).text('Réussi');
			
			root.refreshView();
			
			//console.log(root.getOrdersInfos(1).getStatuCode());
			//console.log(list.length);
		})
		.fail(function(xhr, textStatus, errorThrown) {
			$(msg).text(xhr.responseText);
		}).always(function(){
			$(loadingbox).hide();
		});
	}
	
	
	this.populateTwistyTableInDb = function(){
		$.ajax({
			url: contr_link+"&action=populatetwistylist",
			beforeSend: function( xhr ) {
				//$(loadingimg).show();
				$(loadingbox).show();
				$(msg).text('Remplissage de la table des commandes TWISTY dans la BD');
			}
		})
		.done(function( data ) {
			//console.log(data);
			//$(msg).text('Réussi');
			root.refreshFromDb();
			//$(nonet).hide();
		})
		.fail(function(xhr, textStatus, errorThrown) {
			//console.log(xhr.textStatus);
			//$(msg).text(xhr.responseText);
			
			
			//$(nonet).show();
			alert('Erreur de connexion! \nRéessayer plus tard');
			$(loadingbox).hide();
		}).always(function(){
			//$(loadingimg).hide();
			//$(loadingbox).hide();
		});
	}
	
	this.resetTwistyTableInDb = function(){
		$.ajax({
			url: contr_link+"&action=resettwistytableindb",
			beforeSend: function( xhr ) {

				$(loadingbox).show();
				$(msg).text('Remplissage de la table des commandes TWISTY dans la BD');
			}
		})
		.done(function( data ) {
			//console.log(data);
			//$(msg).text('Réussi');
			root.refreshFromDb();
		})
		.fail(function(xhr, textStatus, errorThrown) {
			//console.log(xhr.responseText);
			//$(msg).text(xhr.responseText);
			alert('Erreur de connexion! \nRéessayer plus tard');
			(loadingbox).hide();
		}).always(function(){
			
		});
	}
	
	
	
	this.show = function(){
		list.forEach(function(item){
			console.log(item.vars);
		});
	}
	
	
	this.refreshView = function(){
		var rootHtmlElement=$('#listtwisty_table .left-row-side  .grid-container');
		$(rootHtmlElement).empty();
		
		//console.log(list.length);
		list.forEach(function(item){
			var el=$('#listtwisty_table .left-row-side  .grid-container .grid-item[attr-order="'+item.vars.id_order+'"]');
			//console.log($(el).length);
			var stat="";
			if(item.vars.product_quantity==item.vars.qte_picked){
				stat="finished";
			}else if(item.vars.product_quantity>item.vars.qte_picked && item.vars.qte_picked != 0){
				stat="inprog";
			}else if(item.vars.product_quantity>item.vars.qte_picked && item.vars.qte_picked == 0){
				stat="blnk";
			}
			
			if($(el).length){
				
				$(el).find('.items-list').append('<tr class="'+stat+'"><td>'+item.vars.product_reference+'</td><td class="left-side">'+item.vars.product_name+'</td><td>'+item.vars.product_quantity+'</td><td>'+item.vars.qte_picked+'</td></tr>');
			}else{
			
				var s='';
				s+='<div class="grid-item" attr-order="'+item.vars.id_order+'">';
				s+='<span class="reset" onclick="javascript:twistylist.resetOrder(\''+item.vars.id_order+'\')"><div class="myinfobulle">Annuler le picking de cette adresse</div></span>';
				s+='<span class="outofstock" onclick="javascript:outofstock(\''+item.vars.id_order+'\')"><div class="myinfobulle">Mettre la commande en out of stock</div></span>';
				
				//s+='<span class="close" onclick="javascript:twistylist.removeOrder(\''+item.vars.id_order+'\')">X</span>';
				s+='<span class="boxname">'+item.vars.id_box+'</span>';
				//s+='<span class="boxname">1</span>';
				
				s+='<span class="num-commande">'+item.vars.id_order+'</span>';
					s+='<table class="items-list">';
						s+='<tr class="detail-row tablehead"><td style="min-width:80px;">Code Article</td><td>Produit</td><td style="min-width:100px;">Qté Commandée</td><td style="min-width:80px;">Qté Piquée</td></tr>';
						s+='<tr class="'+stat+'" >';
							
							//s+='<span>'+val['product_ean13']+'</span>|<span>'+val['qte_picked']+'</span>|<span>'+val['product_quantity']+'</span>';
							s+='<td>'+item.vars.product_reference+'</td><td class="left-side">'+item.vars.product_name+'</td><td>'+item.vars.product_quantity+'</td><td>'+item.vars.qte_picked+'</td>';
							
							//s+='<li><span>'+val['product_ean13']+'</span>|<span>'+val['qte_picked']+'</span>|<span>'+val['product_quantity']+'</span></li>';
						s+='</tr>';
					s+='</table>';
				s+='</div>';
				$(rootHtmlElement).append(s);
			}
		});
		$('#listtwisty_table .left-row-side .grid-container .grid-item').mouseenter(function(){
			//console.log('in');
			$(this).find('.items-list').show();
			$(this).addClass('is_hover');
		});
		$('#listtwisty_table .left-row-side .grid-container .grid-item').mouseleave(function(){
			$(this).find('.items-list').hide();
			$(this).removeClass('is_hover');
		});
		
		root.refreshBoxesColors();
		initResetButtonEvents();
		infoBulleInit();
	}
	
	this.refreshBoxesColors = function(){
		var orders=this.getOrdersIds();
		orders.forEach(function(id_order){
			var color="#FFFFFF";
			if(root.getOrdersInfos(id_order).getStatuCode()==0){
				//console.log('0');
				color=beginColor;
				
			}
			if(root.getOrdersInfos(id_order).getStatuCode()==1){
				//console.log('1');
				color=inProgColor;
			}
			if(root.getOrdersInfos(id_order).getStatuCode()==2){
				//console.log('2');
				color=finishColor;
			}
			var el=$('#listtwisty_table .left-row-side  .grid-container .grid-item[attr-order="'+id_order+'"]');
			$(el).css('background-color', color);
		});
	}
	
	//Choisir la commande la plus proche à finir. 
	this.chooseOrderForPicking = function(ean,is_code_article){
		if(is_code_article){
			console.log('code_article');
			
			var id_order_array=new Array();
			
			
			list.forEach(function(item) {
				// do something with `item`
				if(item.vars.product_reference==ean && item.vars.product_quantity > item.vars.qte_picked){
					id_order_array.push(item.vars.id_order);
				}
			});
			id_order_array=$.unique(id_order_array);
			
			var id=-1;
			var s=0;
			
			for(var i=0;i<id_order_array.length;i++){
				var sum_qte_product=0;
				var sum_qte_picked=0;
				
				list.forEach(function(item) {
					// do something with `item`
					if(item.vars.id_order==id_order_array[i]){
						sum_qte_product+=parseInt(item.vars.product_quantity);
						sum_qte_picked+=parseInt(item.vars.qte_picked);
					}
				});
				
				var v=(parseInt(sum_qte_product)-parseInt(sum_qte_picked));
				if(i==0){
					id=id_order_array[i];
					s=v;
				}else{
					if(v<s){
						id=id_order_array[i];
						s=v;
					}
				}
				
			}
		}else{
			
			var id_order_array=new Array();
			
			
			list.forEach(function(item) {
				// do something with `item`
				if(item.vars.product_ean13==ean && item.vars.product_quantity > item.vars.qte_picked){
					id_order_array.push(item.vars.id_order);
				}
			});
			id_order_array=$.unique(id_order_array);
			
			var id=-1;
			var s=0;
			
			for(var i=0;i<id_order_array.length;i++){
				var sum_qte_product=0;
				var sum_qte_picked=0;
				
				list.forEach(function(item) {
					// do something with `item`
					if(item.vars.id_order==id_order_array[i]){
						sum_qte_product+=parseInt(item.vars.product_quantity);
						sum_qte_picked+=parseInt(item.vars.qte_picked);
					}
				});
				
				var v=(parseInt(sum_qte_product)-parseInt(sum_qte_picked));
				if(i==0){
					id=id_order_array[i];
					s=v;
				}else{
					if(v<s){
						id=id_order_array[i];
						s=v;
					}
				}
				
			}
		}
		return id;
	}
	
	this.pick_item = function(ean,is_code_article){
		console.log('pick_item');
		var id_order=this.chooseOrderForPicking(ean,is_code_article);
		//console.log(id_order);
		if(id_order==-1){
			//boxNameElement.empty();
			boxNameElement.hideIdBox();
			boxNameElement.setErrorMsg('Article non trouvé!');
			boxNameElement.showMsgBox();
			boxNameElement.clignote('#FFFFFF',clignoteColor,false);
			//boxNameElement.setStyle({'font-size': '10px;'});
			//boxNameElement.setId('Article non trouvé!');
		}else{
			if(is_code_article){
				list.forEach(function(item) {
					// do something with `item`
					if(item.vars.id_order==id_order && item.vars.product_reference==ean){
						item.vars.qte_picked=parseInt(item.vars.qte_picked)+1;
						
						boxNameElement.setErrorMsg('');
						boxNameElement.setId(item.vars.id_box);
						boxNameElement.showIdBox();
						
						
						if(root.getOrdersInfos(item.vars.id_order).getStatuCode()==0){
							//console.log('0');
							$("#right-box").css('background-color',beginColor);
							boxNameElement.clignote('#FFFFFF',beginColor,true);
						
						}
						if(root.getOrdersInfos(id_order).getStatuCode()==1){
							//console.log('1');
							$("#right-box").css('background-color',inProgColor);
							boxNameElement.clignote('#FFFFFF',inProgColor,true);
						}
						if(root.getOrdersInfos(item.vars.id_order).getStatuCode()==2){
							//console.log('2');
							$("#right-box").css('background-color',finishColor);
							boxNameElement.clignote('#FFFFFF',finishColor,true);
						}
						//console.log();
						
						if($.inArray(item.vars.id_twisty,history)==-1)
							history.push(item.vars.id_twisty);
						
						root.checkAndSave(2);
						
						root.refreshView();
						
						var orderTwisty1=root.getOrdersInfos(id_order);
						var orderState=orderTwisty1.getStatuCode();
						
						//console.log('order state code '+orderState)
						if(orderState==2){
							orderTwisty1.ChangeStatToPicked();
						}
						return false; 
					}
				});				
			}else{
				list.forEach(function(item) {
					// do something with `item`
					if(item.vars.id_order==id_order && item.vars.product_ean13==ean){
						item.vars.qte_picked=parseInt(item.vars.qte_picked)+1;
						
						boxNameElement.setErrorMsg('');
						boxNameElement.setId(item.vars.id_box);
						boxNameElement.showIdBox();
						
						if(root.getOrdersInfos(item.vars.id_order).getStatuCode()==0){
							//console.log('0');
							$("#right-box").css('background-color',beginColor);
							boxNameElement.clignote('#FFFFFF',beginColor,true);
						}
						if(root.getOrdersInfos(id_order).getStatuCode()==1){
							//console.log('1');
							$("#right-box").css('background-color',inProgColor);
							boxNameElement.clignote('#FFFFFF',inProgColor,true);
						}
						if(root.getOrdersInfos(item.vars.id_order).getStatuCode()==2){
							//console.log('2');
							$("#right-box").css('background-color',finishColor);
							boxNameElement.clignote('#FFFFFF',finishColor,true);
						}
						//console.log();
						
						if($.inArray(item.vars.id_twisty,history)==-1)
							history.push(item.vars.id_twisty);
						
						root.checkAndSave(2);
						
						root.refreshView();
						
						var orderTwisty1=root.getOrdersInfos(id_order);
						var orderState=orderTwisty1.getStatuCode();
						
						//console.log('order state code '+orderState)
						if(orderState==2){
							orderTwisty1.ChangeStatToPicked();
						}
						return false; 
					}
				});
			}
		}
		//console.log(history);
	}
	
	this.saveToDb = function(myurl=null){
		//console.log('save');
		if($(history).length>0 && !is_db_occuped){
			var list_copy=list.slice();
			var history_copy=history.slice();
			//console.log("history_copy:"+history_copy);
			
			
			
			$.ajax({
				type: 'POST',
				url: contr_link+"&action=savetodb",
				data: { list_json_copy: JSON.stringify(list_copy), history_json_copy: JSON.stringify(history_copy) },
				beforeSend: function( xhr ) {
					//console.log("beforesend");
					is_db_occuped=true;
					
				}
			})
			.done(function( data ) {
				//console.log( "Success \n" + data );
				console.log('Saved');
				history=[];
				//$.cookie("mycookie", null);
				//$(noconnexion).hide();
				//console.log(nonet);
				$(nonet).hide();
			}).fail(function(){
				//history=history.concat(history_copy).unique();
				//history=history.concat(history_copy);
				//console.log(list_copy);
				//var s=
				//$.cookie("mycookie", JSON.stringify(history_copy));
				
				//alert('Problem while saving to db.');
				//console.log("-"+nonet);
				$(nonet).show();
				console.log('Not Saved');
			}).always(function(){
				//console.log(history_copy);
				is_db_occuped=false;
				if(myurl!=null)
					window.location.href = myurl;
			});
			
		}else{
			//console.log('there is no changes');
			if(myurl!=null)
					window.location.href = myurl;
		}
	}
	
	this.checkAndSave = function(count){
		if($(history).length==count  && !is_db_occuped){
			//console.log($(history).length+","+count);
			root.saveToDb();
		}
	}
	
	this.removeAllTwistyOrdersRows = function(){
		console.log('removeAllTwistyOrdersRows');
		/*
		$.ajax({
			url: contr_link+"&action=removetwistylist",
			beforeSend: function( xhr ) {
				//alert( 'beforesend' );
			}
		})
		.done(function( data ) {
			$('#listtwisty_table').html(data);
			//alert( data );
		})
		.fail(function() {
			alert( "ERROR" );
		});*/
	}
	
	this.resetOrder =function(id_order){
		console.log('Reset'+id_order);
		$.ajax({
			type: 'POST',
			url: contr_link+"&action=resetOrder",
			data: { id_order: id_order },
			beforeSend: function( xhr ) {
				//console.log("beforesend");
				is_db_occuped=true;
				$(loadingbox).show();
				
			}
		})
		.done(function( data ) {
			//console.log( "Success \n" + data );
			console.log(data);
			root.refreshFromDb();
		}).fail(function(){
			console.log('Problem ajax.');
		}).always(function(){
			is_db_occuped=false;
			$(loadingbox).hide();
		});
	}
	this.removeOrder =function(id_order){
		console.log('Remove'+id_order);
	}
	
}

	/*
	twistyorder.prototype = {
		some_property: null,
		some_other_property: 10,

		doSomething: function(msg) {
			this.some_property = msg;
			alert(this.some_other_property);
		}
	}; 
	*/
	
	
function init(){
	
	
	boxNameElement=new BoxNameDiv();
	
	//$(loadingimg).show();
	twistylist=new TwistyList();
	//
	twistylist.populateTwistyTableInDb();
	//
	//twistylist.refreshFromDb();
	//twistylist.refreshView();
	
	
	
	(window.resetCompteur=function(){
		if(compteur!=null)
			clearInterval(compteur);
		compteur=setInterval(twistylist.saveToDb, 5000);
	})();
	
	(window.resetCookieCompteur=function(){
		if(resetCookieCompteur!=null)
			clearInterval(resetCookieCompteur);
		resetCookieCompteur=setInterval(reset_cookie, 240000);
	})();
	//console.log(twistylist.getOrdersIds());
}

function reset_cookie(){
	
	//alert();
	
	
	
	$.ajax({
		type: 'POST',
		url: contr_link+"&action=reset_cookie",
		data: { },
		beforeSend: function( xhr ) {
			//console.log("beforesend");
			//is_db_occuped=true;
			
		}
	})
	.done(function( data ) {
		console.log( data );
		
	}).fail(function(){
		//history=history.concat(history_copy).unique();
		console.log('Problem .');
	}).always(function(){
		
	});
	
}

$(document).ready(function(){
	
	
	
	configureEanInput();
	initInputKeys();
	catchRedirectEvent();
	
	
	$('#content').css('padding-top','10px');
	
	loadingimg=$('#debug-bar .loading-img');
	loadingbox=$('#myloading');
	msg=$('#debug-bar .msg');

	init();
	
	$('#msgcache').css({'position':'fixed','right':'5px','bottom':'5px','display':'none'});
	
	
	/*if($.cookie("mycookie")){
		$('div#msgcache div.container').empty();
		$('div#msgcache div.container').append('<h2>'+$.cookie("mycookie")+'</h2>');
		
		//alert();
	}*/
	
});
 
function infoBulleInit(){
	
	//$(".outofstock").append('<div class="myinfobulle" style="display: none;">Mettre la commande en Out Of Stock</div>');
	$('.outofstock').mouseenter(function(){
		//console.log('enter');
		$(this).find('.myinfobulle').show(50);
		
		//console.log('KKK');
	}).mouseleave(function(){
		//console.log('leave');
		$(this).find('.myinfobulle').hide();
		//console.log('MMM');
	});
}

 
window.initResetButtonEvents = function(){
	console.log('initOtherComponents');
	$('.reset').mouseenter(function(){
		//console.log('enter');
		$(this).find('.myinfobulle').show(50);
	}).mouseleave(function(){
		//console.log('leave');
		$(this).find('.myinfobulle').hide();
	});
}
//FUNCTIONS FOR EAN INPUT

function configureEanInput(){
	$('#formean').submit(function(){
		/*if($(nonet).is(":visible")){
			return false;
		}*/
		var len=($( "#ine" ).val()).length;
		
		if(len>=6 && len<=12){
			submit_code_article($( "#ine" ).val());
	    }else if(len==13){
			submit_ean13($( "#ine" ).val());
	    }else if(len==30){
			submit_ean($( "#ine" ).val());
	    }else if(len==31){
            var eee=$( "#ine" ).val().substring(1);
            submit_ean(eee);
        }
        return false;
    });
}

function catchRedirectEvent(){
	$('a[href!="javascript:void(0);"][href!="javascript:void(0)"]:not(.dropdown-toggle,.list-toolbar-btn)').click(function(){
		twistylist.saveToDb($(this).attr('href'));
		return false;
	});
	
}


function submit_code_article(ean){
	//console.log('code_article');
	twistylist.pick_item(ean,true);
}

function submit_ean13(ean){
	//console.log('ean13');
	twistylist.pick_item(ean,false);
	
}

function submit_ean(ean){
	ean=format_ene(ean);
	
	
	//pick_item(id_order,ine,el);
	submit_ean13(ean);
	//twistylist.pick_item(ean,2);
	
	/*
	var el=$('#listtwisty_table .right-row-side');
	if(id_order>-1){
		////
		pick_item(id_order,ine,el);
		resetCompteur();
		increment_changes_and_check();
		refreshTable();
	}else{
		////
		$(el).find('.info-box  .inner').text('__');
		$(el).find('.info-error').text(item_not_found_msg);
	}
		*/
}

window.format_ene=function(ine){
	//console.log(ine);
	ine=ine.substring(3, 16);
	return ine;
	
}

//FUNCTIONS FOR CATCH KEYS TAPS
var validKeys=['0','1','2','3','4','5','6','7','8','9'];
var shift=false;

function handle(e) {
	//if (form.elements[e.type + 'Ignore'].checked) return;

	
	//console.log("TYPE: "+e.type+" key: "+e.key+" code: "+e.code+" shiftKey: "+e.shiftKey+" ctrlKey: "+e.ctrlKey+" altKey: "+e.altKey+" metaKey " + e.metaKey+" repeat: "+e.repeat+"\n");
	//console.log(e.type);
	//console.log(e.key);
	
	/*
	if(new_search){
		
		
			$( "#ine" ).val('');
		
	}
	disable_new_search();
	*/
	
	if(e.type=="keydown"){
		
		if(jQuery.inArray(e.key, validKeys) !== -1){
			if(new_search){
				
				if(!shift){
					$( "#ine" ).val('');
				}
			}
			disable_new_search();
		}else if(e.key=="Enter" || e.key=="Control" || e.key=="v" || e.key=="V" || e.key=="c" || e.key=="C" || e.key=="Backspace"){
			return true;
		}else if(e.key=="CapsLock"){
			shift=true;
		}else{
			return false;
		}
	}
	if(e.type=="keyup"){
		if(e.key=="CapsLock"){
			shift=false;
		}
	}
}


function initInputKeys(){
	
	$( document ).keypress(function(e) {
		
		//$(noconnection).show();
		
		if (!$(e.target).closest("input")[0] && e.which>=48 && e.which<=57) {
			
			$( "#ine" ).val('');
			$( "#ine" ).focus().val(String.fromCharCode(e.which));
			disable_new_search();
			
			return false;
		}
	});

	var kinput = document.getElementById('ine');
	$(kinput).attr('autocomplete', 'off');
	kinput.onkeydown = kinput.onkeyup = kinput.onkeypress = handle;
	/*
	$('#ine').keypress(function(e) {
		console.log( "[TWISTY]"+ e.which );
		if ((e.which>=48 && e.which<=57) ||  e.which==8  ||  e.which==0  ) {
			if(new_search){
				//console.log('NEW');
				$( "#ine" ).val('');
			}else{
				//console.log('AAA');
			}
			disable_new_search();
		}else if(e.which==114){
			console.log('SHIFT');
		}else if(e.which==13){
			return true;
		}else {
			return false;
		}
		
		
	});*/

	window.disable_new_search=function(){
		//var new_search=true;
		new_search=false;
		if(new_search_compteur!=null)
			clearTimeout(new_search_compteur);
		new_search_compteur=setTimeout( function(){ 
			// Do something after 1 second 
			new_search=true;
			//console.log(new_search);
		}  , 500 );
		
	}
	
}
window.download_pdf=function() {
	var el=$('#basic-table tbody');
	$(el).empty();
	
	//var tmp_array = new Array();
	
	
	//getOrdersInfos
	var ordersIds=twistylist.getOrdersIds();
	
	
	var image="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAIBAQIBAQICAgICAgICAwUDAwMDAwYEBAMFBwYHBwcGBwcICQsJCAgKCAcHCg0KCgsMDAwMBwkODw0MDgsMDAz/2wBDAQICAgMDAwYDAwYMCAcIDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAz/wAARCAAZABkDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwDw3/g3p/4N6fgv/wAFZv2L/E/xG+I3if4oaLrei+Nbrw3BB4b1GxtrV7eKxsLhXZZ7OZzIXupASHAwF+UEEn7w/wCIKn9lj/ofv2gP/B5pH/yso/4Mqf8AlFl4+/7KrqP/AKaNHr9fqAP54f8Agsz/AMGv3wC/4J2/8E2PiR8Y/BXi/wCMGqeJ/B/9mfYrXW9V06ewl+06pZ2cnmJDYxSHEdw5G2RcMFJyMg/hDX9fv/B0d/ygo+Of/cA/9SHTK/kCoA/d7/g1+/4LM/s2f8E7f2BfF/gr4x/Ej/hD/E+qfEC81u1sv+Ef1TUPNs5NO02FJfMtbaWMZkt5l2lgw2ZIwQT+j/8AxFHfsJ/9Fz/8szxD/wDINfyBUUAf0ff8F6v+C9X7J37aP/BJ74rfDP4afFb/AISXxt4l/sj+zdN/4RnWLP7T5GsWNzL+9uLSOJdsMMjfM4ztwMkgH+cGiigD/9k=";
	
	
	ordersIds.forEach(function(id){
		//console.log(in_array(item['id_order'],tmp));
		var order=twistylist.getOrdersInfos(id);
		//console.log(order.getStatuCode());
		if(order.getStatuCode()==2){
			//console.log(order.total_qte_picked());
			//total_paid_tax_incl
			$(el).append('<tr><td class="col1" align="right">'+id+'</td><td>'+order.getId_box()+'</td><td>'+order.getPayment()+'</td><td>'+(parseFloat(order.getTotal_paid_tax_incl())).toFixed(2)+'</td><td>'+(parseFloat(order.getTotal_shipping())).toFixed(2)+'</td><td>'+order.getTotal_product_quantity()+'</td><td>'+order.getTotal_qte_picked()+'</td><td><img src="'+''+'" /></td><td></td></tr>');
		}
		/*if(in_array(item['id_order'],finished_orders_array)>-1 && in_array(item['id_order'],tmp)===false){
			//console.log(item);
			tmp.push(item['id_order']);
			$(el).append('<tr><td class="col1" align="right">'+item['id_order']+'</td><td>'+item['id_box']+'</td><td>'+item['payment']+'</td><td>'+item['total_shipping']+'</td><td>'+get_total_article(item['id_order'])+'</td><td>'+get_total_picked(item['id_order'])+'</td><td><input type="checkbox" /></td><td></td></tr>');
		}*/
		//console.log(id);
	});
	

	//$('#basic-table tbody').append();
	
	var doc = new jsPDF('p', 'pt');
	var res = doc.autoTableHtmlToJson(document.getElementById("basic-table"));
	
	var imgElements = document.querySelectorAll('#basic-table tbody img');
	var images = [];
	var i = 0;
  //var header = function(data) {
	//doc.setFontSize(18);
	//doc.setTextColor(40);
	//doc.setFontStyle('normal');
	//doc.addImage(headerImgData, 'JPEG', data.settings.margin.left, 20, 50, 50);
	//doc.text("Testing Report", data.settings.margin.left, 50);
  //};

	var options = {
	//addPageContent : header,
		theme: 'grid',
		styles:{
			fontSize: 7,
			halign: 'center',
			overflow: 'linebreak', 
			columnWidth: 'wrap'
		},
		headerStyles: {
			fontSize: 7,
			halign: 'center'
		},
		margin: {
		  top: 10
		},
		columnStyles: {
			0: {columnWidth: 40},
			1: {columnWidth: 40},
			2: {columnWidth: 110},
			3: {columnWidth: 60},
			4: {columnWidth: 60},
			5: {columnWidth: 40},
			6: {columnWidth: 40},
			7: {columnWidth: 20},
			8: {columnWidth: 130},
			// etc
		},
		createdHeaderCell: function (cell, data) {
				//console.log();
				if($.inArray('col1',(cell.raw.className).split(" "))>-1){
					//console.log(cell.styles);
					cell.styles.columnWidth= 'wrap';
				}
				/*console.log();
				if (cell.text == 'ID ORDER') {
					cell.styles.fontSize= 15;
					cell.styles.textColor = 111;
					//console.log("01");
				} else {//else rule for drawHeaderCell hook
					cell.styles.textColor = 255;
					cell.styles.fontSize = 10;
					//console.log("02");
				}*/
				
		},
		/*drawCell: function(cell,data){
			
		},*/
		/*bodyStyles: {rowHeight: 30},*/
		drawCell: function(cell, opts) {
			if($.inArray('col1',(cell.raw.className).split(" "))>-1){
				console.log(cell);
				cell.styles.halign= 'center';
			}
			if (opts.column.dataKey === 5) {
				images.push({
				  url: image,
				  x: cell.textPos.x,
				  y: cell.textPos.y
				});
				i++;
			}
		},
		addPageContent: function() {
		  for (var i = 0; i < images.length; i++) {
			doc.addImage(images[i].url, images[i].x+65, images[i].y, 10, 10);
		  }
		},
		startY: doc.autoTableEndPosY() + 10
	};

	doc.autoTable(res.columns, res.rows, options);

	doc.save("table.pdf");
}
window.removefinishedorders=function() { 
	if(twistylist.getFinishedOrdersIds().length==0){
		alert("Aucune Commande terminée trouvée!");
		return;
	}
	if (!confirm('N\'oublie pas d\'imprimer la liste avant de libérer les commandes.\nVoulez vous libérer?'))
		return;
	
	$.ajax({
		type: 'POST',
		url: contr_link+"&action=removefinishedorders",
		data: { list_orders_ids: JSON.stringify(twistylist.getFinishedOrdersIds()), list_twisty: JSON.stringify(twistylist.getFinishedTwistyIds())},
		beforeSend: function( xhr ) {
			//console.log("beforesend");
			//is_db_occuped=true;
			download_pdf();
			$(loadingbox).show();
		}
	})
	.done(function( data ) {
		//console.log( data );
		twistylist.refreshFromDb();
//		$(nonet).hide();	
	}).fail(function(){
		//history=history.concat(history_copy).unique();
		alert('Erreur de connexion! \nRéessayer plus tard');
		$(loadingbox).hide();
	}).always(function(){
		is_db_occuped=false;
		//$(loadingbox).hide();
	});
	
}

window.settooutofstock = function(){
	if(twistylist.getNotFinishedOrdersIds().length==0){
		alert("Aucune Commande non terminée trouvée!");
		return;
	}
	
	if (!confirm('Voulez vous vraiment libérer les adresses non terminées.\n(Les status des commandes non terminées seront changées!). \n\nContinuer ?'))
		return;
	
	
	$.ajax({
		type: 'POST',
		url: contr_link+"&action=removeoutofstockorders",
		data: { list_orders_ids: JSON.stringify(twistylist.getNotFinishedOrdersIds())},
		beforeSend: function( xhr ) {
			//console.log("beforesend");
			is_db_occuped=true;
			$(loadingbox).show();
		}
	})
	.done(function( data ) {
		//console.log( data );
		//twistylist.refreshFromDb();
		twistylist.refreshFromDb();
			
	}).fail(function(){
		//history=history.concat(history_copy).unique();
		alert('Erreur de connexion! \nRéessayer plus tard');
		$(loadingbox).hide();
	}).always(function(){
		is_db_occuped=false;
		
	});
	
}
window.outofstock  = function(id){
	
	if (!confirm('Voulez vous vraiment continuer ?'))
		return;
	
	
	$.ajax({
		type: 'POST',
		url: contr_link+"&action=removeoutofstockorder",
		data: { id: id},
		beforeSend: function( xhr ) {
			//console.log("beforesend");
			is_db_occuped=true;
			$(loadingbox).show();
		}
	})
	.done(function( data ) {
		console.log( data );
		//twistylist.refreshFromDb();
		twistylist.refreshFromDb();
			
	}).fail(function(){
		//history=history.concat(history_copy).unique();
		console.log('Problem .');
	}).always(function(){
		is_db_occuped=false;
		$(loadingbox).hide();
	});
	
}