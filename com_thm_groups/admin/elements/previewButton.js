/**
 * @category	Web Programming Weeks WS2013/2014: TH Mittelhessen
 * @package		com_thm_groups
 * @author		Adnan Özsarigöl, <adnan.oezsarigoel@mni.thm.de>
 * @author		Bünyamin Akdağ,  <buenyamin.akdag@mni.thm.de>
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link		http://www.mni.thm.de
 */


 /**
 * Show Preview
 * 
 * @todo   Backup database table row of current id
 * @todo   restore database table row from backup and delete backup
 */
var thmGroupsProfilePreview = function() {
	
	var self = this;	
	var itemId;
	var token;
	
	// Get current Item ID
	this.getItemId = function() {
		itemId = $('#jform_id').val();
		if (typeof itemId === 'undefined') {
			return false;
		}
		return itemId;
	}
	
	// Get Token
	this.getToken = function() {		
		return token;
	}
	
	// Set Token
	this.setToken = function(value) {
		// Validate token
		if (value.length === 32) {
			token = value;
			return true;
		}
		return false;
	}
	
	// Delete Token
	this.deleteToken = function() {		
		token = false;
	}
	
	// Open Profile Preview
	this.open = function() {
		
		if (this.getItemId() === false) {
			alert('An unexpected error occured!\nNo Item-ID found!');
		}
		
		var url = false;

		// Lock Window with empty Popup
		SqueezeBox.open(url, {
			handler: 'iframe',
			size: {x: 240, y: 160},
			sizeLoading: {x: 0, y: 0},
			closable: false,
			closeBtn: false,
		});

		// Submit ID to Server
		url = '../index.php?option=com_thm_groups&view=advanced&format=raw&task=notify';
		$.post(url, {Itemid: this.getItemId()}, function(token) {
			
			// Set token
			if (self.setToken(token) !== true) {
				alert('An unexpected error occured!\nToken not valid!');
				return;
			}
			
			// Submit form to save data of current id
			var form = $('#item-form');
			form.find('input[name=task]').val('item.apply');
			
			url = false;
			$.post(url, form.serialize(), function(data, textstatus, xhrReq) {				
				var itemId = self.getItemId();
				if (itemId == false) {
					var page = $(xhrReq.responseText);
					itemId = $('#jform_id', page).val();
				}
				
				// Open Preview
				url = '../index.php?option=com_thm_groups&view=advanced&Itemid=' + itemId + '&notifytoken=' + self.getToken();
				self.deleteToken();
				SqueezeBox.open(url, {
					handler: 'iframe',
					size: {x: 1020, y: 600},
					sizeLoading: {x: 240, y: 160},
					closable: true,
					closeBtn: true,
				});
			});
		});
	}
}

var ProfilePreview = new thmGroupsProfilePreview();