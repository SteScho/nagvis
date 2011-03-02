/*****************************************************************************
 *
 * NagVisObject.js - This class handles the visualisation of Nagvis objects
 *
 * Copyright (c) 2004-2011 NagVis Project (Contact: info@nagvis.org)
 *
 * License:
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 *
 *****************************************************************************/
 
/**
 * @author	Lars Michelsen <lars@vertical-visions.de>
 */

var NagVisObject = Base.extend({
	parsedObject:          null,
	hover_template_code:   null,
	context_template_code: null,
	conf:                  null,
	contextMenu:           null,
	lastUpdate:            null,
	firstUpdate:           null,
	bIsFlashing:           false,
	bIsLocked:             true,
	objControls:           null,
	childs:                null,
	
	constructor: function(oConf) {
		// Initialize
		this.setLastUpdate();
		
		this.childs      = [];
		this.objControls = [];
		this.conf        = oConf;
		
		// When no object_id given by server: generate own id
		if(this.conf.object_id == null)
			this.conf.object_id = getRandomLowerCaseLetter() + getRandom(1, 99999);
		
		// Load view specific config modifiers (Normaly triggered by url params)
		this.loadViewOpts();
	},
	
	/**
	 * PUBLIC loadViewOpts
	 *
	 * Loads view specific options. Basically this options are triggered by url params
	 *
	 * @author	Lars Michelsen <lars@vertical-visions.de>
	 */
	loadViewOpts: function() {
		// View specific hover modifier set. Will override the map configured option
		if(oViewProperties && oViewProperties.enableHover && oViewProperties.enableHover != '')
			this.conf.hover_menu = '0';
		
		// View specific hover modifier set. Will override the map configured option
		if(oViewProperties && oViewProperties.enableHover && oViewProperties.enableHover != '')
			this.conf.context_menu = '0';
	},
	
	/**
	 * PUBLIC setLastUpdate
	 *
	 * Sets the time of last status update of this object
	 *
	 * @author	Lars Michelsen <lars@vertical-visions.de>
	 */
	setLastUpdate: function() {
		this.lastUpdate = iNow;
		
		// Save datetime of the first state update (needed for hover parsing)
		if(this.firstUpdate === null)
			this.firstUpdate = this.lastUpdate;
	},
  
	/**
	 * PUBLIC getContextMenu()
	 *
	 * Creates a context menu for the object
	 *
	 * @author	Lars Michelsen <lars@vertical-visions.de>
	 */
	getContextMenu: function (sObjId) {
		// Only enable context menu when configured
		if(this.conf.context_menu && this.conf.context_menu == '1') {
			// Writes template code to "this.context_template_code"
			this.getContextTemplateCode();
			
			// Replace object specific macros
			this.replaceContextTemplateMacros();
			
			var doc = document;
			var oObj = doc.getElementById(sObjId);
			var oContainer = doc.getElementById(this.conf.object_id);
			
			if(oObj == null) {
				eventlog("NagVisObject", "critical", "Could not get context menu object (ID:"+sObjId+")");
				return false;
			}
			
			if(oContainer == null) {
				eventlog("NagVisObject", "critical", "Could not get context menu container (ID:"+this.conf.object_id+")");
				oObj = null; 
				return false;
			}
			
			// Only create a new div when the context menu does not exist
			var contextMenu = doc.getElementById(this.conf.object_id+'-context');
			var justAdded = false;
			if(!contextMenu) {
				// Create context menu div
				var contextMenu = doc.createElement('div');
				contextMenu.setAttribute('id', this.conf.object_id+'-context');
				contextMenu.setAttribute('class', 'context');
				contextMenu.setAttribute('className', 'context');
				contextMenu.style.zIndex = '1000';
				contextMenu.style.display = 'none';
				contextMenu.style.position = 'absolute';
				contextMenu.style.overflow = 'visible';
				justAdded = true;
			}
			
			// Append template code to context menu div
			contextMenu.innerHTML = this.context_template_code;
			
			if(justAdded) {
				// Append context menu div to object container
				oContainer.appendChild(contextMenu);
			
				// Add eventhandlers for context menu
				oObj.onmousedown = contextMouseDown;
				oObj.oncontextmenu = contextShow;
			}
			
			contextMenu = null;
			oContainer = null;
			oObj = null;
			doc = null;
		}
  },
	
	/**
	 * replaceContextTemplateMacros()
	 *
	 * Replaces object specific macros in the template code
	 *
	 * @return	String		HTML code for the hover box
	 * @author	Lars Michelsen <lars@vertical-visions.de>
	 */
	replaceContextTemplateMacros: function() {
		var oSectionMacros = {};
		
		// Break when no template code found
		if(!this.context_template_code || this.context_template_code === '') {
			return false;
		}
		
		var oMacros = {
			'obj_id':      this.conf.object_id,
			'name':        this.conf.name,
			'address':     this.conf.address,
			'html_cgi':    this.conf.htmlcgi,
			'backend_id':  this.conf.backend_id,
			'custom_1':    this.conf.custom_1,
			'custom_2':    this.conf.custom_2,
			'custom_3':    this.conf.custom_3
		};

	  if(typeof(oPageProperties) != 'undefined' && oPageProperties != null 
		   && (oPageProperties.view_type === 'map' || oPageProperties.view_type === 'automap'))
			oMacros.map_name = oPageProperties.map_name;
		
		if(this.conf.type === 'service') {
			oMacros.service_description = escapeUrlValues(this.conf.service_description);
			
			oMacros.pnp_hostname = this.conf.name.replace(/\s/g,'%20');
			oMacros.pnp_service_description = this.conf.service_description.replace(/\s/g,'%20');
		} else
			oSectionMacros.service = '<!--\\sBEGIN\\sservice\\s-->.+?<!--\\sEND\\sservice\\s-->';
		
		// Macros which are only for hosts
		if(this.conf.type === 'host')
			oMacros.pnp_hostname = this.conf.name.replace(/\s/g,'%20');
		else
			oSectionMacros.host = '<!--\\sBEGIN\\shost\\s-->.+?<!--\\sEND\\shost\\s-->';

		if(oPageProperties.view_type === 'automap')
			oSectionMacros.not_automap = '<!--\\sBEGIN\\snot_automap\\s-->.+?<!--\\sEND\\snot_automap\\s-->';
		if(this.conf.view_type !== 'line')
			oSectionMacros.line = '<!--\\sBEGIN\\sline\\s-->.+?<!--\\sEND\\sline\\s-->';
		if(this.conf.view_type !== 'line' || (this.conf.line_type == 11 || this.conf.line_type == 12))
			oSectionMacros.line_type = '<!--\\sBEGIN\\sline_two_parts\\s-->.+?<!--\\sEND\\sline_two_parts\\s-->';

		// Replace hostgroup range macros when not in a hostgroup
		if(this.conf.type !== 'hostgroup')
			oSectionMacros.hostgroup = '<!--\\sBEGIN\\shostgroup\\s-->.+?<!--\\sEND\\shostgroup\\s-->';

		// Replace servicegroup range macros when not in a servicegroup
		if(this.conf.type !== 'servicegroup')
			oSectionMacros.servicegroup = '<!--\\sBEGIN\\sservicegroup\\s-->.+?<!--\\sEND\\sservicegroup\\s-->';

		// Replace map range macros when not in a hostgroup
		if(this.conf.type !== 'map')
			oSectionMacros.map = '<!--\\sBEGIN\\smap\\s-->.+?<!--\\sEND\\smap\\s-->';
		
		// Loop and replace all unwanted section macros
		for (var key in oSectionMacros) {
			var regex = getRegEx('section-'+key, oSectionMacros[key], 'gm');
			this.context_template_code = this.context_template_code.replace(regex, '');
			regex = null;
		}
		oSectionMacros = null;
		
		// Loop and replace all normal macros
		this.context_template_code = this.context_template_code.replace(/\[(\w*)\]/g, 
		                             function(){ return oMacros[ arguments[1] ] || '';});
		oMacros = null;
	},
	
	/**
	 * getContextTemplateCode()
	 *
	 * Get the context template from the global object which holds all templates of 
	 * the map
	 *
	 * @return	String		HTML code for the hover box
	 * @author	Lars Michelsen <lars@vertical-visions.de>
	 */
	getContextTemplateCode: function() {
		this.context_template_code = oContextTemplates[this.conf.context_template];
	},
	
	/**
	 * PUBLIC getHoverMenu
	 *
	 * Creates a hover box for objects
	 *
	 * @author	Lars Michelsen <lars@vertical-visions.de>
	 */
	getHoverMenu: function (sObjId) {
		// Only enable hover menu when configured
		if(!this.conf.hover_menu || this.conf.hover_menu != '1')
			return;

		var objId = this.conf.object_id;
		var sTemplateCode;
		var iHoverDelay = this.conf.hover_delay;
		
		// Parse the configured URL or get the hover menu
		if(this.conf.hover_url && this.conf.hover_url !== '') {
			this.getHoverUrlCode();
			
			sTemplateCode = this.hover_template_code;
		} else {
			// Only fetch hover template code and parse static macros when this is
			// no update
			if(this.hover_template_code === null)
				this.getHoverTemplateCode();
			
			// Replace dynamic (state dependent) macros
			sTemplateCode = replaceHoverTemplateDynamicMacros(this);
		}
		
		var doc = document;
		var oObj = doc.getElementById(sObjId);
		var oContainer = doc.getElementById(this.conf.object_id);
		
		if(oObj == null) {
			eventlog("NagVisObject", "critical", "Could not get hover menu object (ID:"+sObjId+")");
			return false;
		}
		
		if(oContainer == null) {
			eventlog("NagVisObject", "critical", "Could not get hover menu container (ID:"+this.conf.object_id+")");
			oObj = null; 
			return false;
		}
		
		// Only create a new div when the hover menu does not exist
		var hoverMenu = doc.getElementById(this.conf.object_id+'-hover');
		var justCreated = false;
		if(!hoverMenu) {
			// Create hover menu div
			var hoverMenu = doc.createElement('div');
			hoverMenu.setAttribute('id', this.conf.object_id+'-hover');
			hoverMenu.setAttribute('class', 'hover');
			hoverMenu.setAttribute('className', 'hover');
			hoverMenu.style.zIndex = '1000';
			hoverMenu.style.display = 'none';
			hoverMenu.style.position = 'absolute';
			hoverMenu.style.overflow = 'visible';
			justCreated = true;
		}
		
		// Append template code to hover menu div
		hoverMenu.innerHTML = sTemplateCode;
		sTemplateCode = null;
		
		if(justCreated) {
			// Append hover menu div to object container
			oContainer.appendChild(hoverMenu);
		
			// Add eventhandlers for hover menu
			if(oObj) {
				oObj.onmousemove = function(e) { var id = objId; var iH = iHoverDelay; displayHoverMenu(e, id, iH); id = null; iH = null; };
				oObj.onmouseout = function() { hoverHide(); };
			}
		}

		justCreated = null;
		hoverMenu = null;
		oContainer = null;
		oObj = null;
		doc = null;
	},
	
	/**
	 * getHoverUrlCode()
	 *
	 * Get the hover code from the hover url
	 *
	 * @return	String		HTML code for the hover box
	 * @author	Lars Michelsen <lars@vertical-visions.de>
	 */
	getHoverUrlCode: function() {
		this.hover_template_code = oHoverUrls[this.conf.hover_url];
		
		if(this.hover_template_code === null)
			this.hover_template_code = '';
	},
	
	/**
	 * getHoverTemplateCode()
	 *
	 * Get the hover template from the global object which holds all templates of 
	 * the map
	 *
	 * @return	String		HTML code for the hover box
	 * @author	Lars Michelsen <lars@vertical-visions.de>
	 */
	getHoverTemplateCode: function() {
		// Asign the template code and replace only the static macros
		// These are typicaly configured static configued values from nagios
		this.hover_template_code = replaceHoverTemplateStaticMacros(this, oHoverTemplates[this.conf.hover_template]);
	},

	/**
	 * Locks/Unlocks the object and fires dependent actions
	 *
	 * @author  Lars Michelsen <lars@vertical-visions.de>
	 */
	toggleLock: function(lock) {
		if(isset(lock))
			this.bIsLocked = lock;
		else
			this.bIsLocked = !this.bIsLocked;

		if(this.toggleObjControls())
			return this.bIsLocked ? -1 : 1;
		else
			return 0;
	},

	/**
	 * Shows or hides all object controls of a map object depending
	 * on the lock state of this object.
	 *
	 * @author  Lars Michelsen <lars@vertical-visions.de>
	 */
	toggleObjControls: function() {
		if(!this.bIsLocked) {
			if(isset(this.parseControls)) {
				this.parseControls();
				return true;
			}
		} else {
			if(isset(this.removeControls)) {
				this.removeControls();
				return true;
			}
		}
		return false;
	},

	/**
	 * This method parses a given coordinate which can be a simple integer
	 * which is simply returned or a reference to another object and/or
	 * a specified anchor of the object.
	 *
	 * @author  Lars Michelsen <lars@vertical-visions.de>
	 */
	parseCoord: function(val, dir) {
		if(!isRelativeCoord(val)) {
			return parseInt(val);
		} else {
			// This must be an object id. Is there an offset given?
			if(val.search('%') !== -1) {
				var parts     = val.split('%');
				var objectId  = parts[0];
				var offset    = parts[1];
				var refObj    = getMapObjByDomObjId(objectId);
				if(refObj)
					return parseFloat(refObj.parseCoord(refObj.conf[dir], dir)) + parseFloat(offset);
				else
					return 0;
			} else {
				// Only an object id. Get the coordinate and return it
				var refObj = getMapObjByDomObjId(val);
				if(refObj)
					return parseInt(refObj.parseCoord(refObj.conf[dir], dir));
				else
					return 0;
			}
		}
	},

	/**
	 * Wrapper for the parseCoord method to parse multiple coords at once
	 * e.g. for lines.
	 *
	 * @author  Lars Michelsen <lars@vertical-visions.de>
	 */
	parseCoords: function(val, dir) {
		var l = val.split(',');

		for(var i = 0, len = l.length; i < len; i++)
			l[i] = this.parseCoord(l[i], dir);

		return l;
	},

	// Transform the current coords to absolute coords when relative
	makeAbsoluteCoords: function(num) {
		var x = num === -1 ? this.conf.x : this.conf.x.split(',')[num];
		var y = num === -1 ? this.conf.y : this.conf.y.split(',')[num];

		// Skip when already absolute
		if(!isRelativeCoord(x) && !isRelativeCoord(y))
			return;

		// Get parent object ids
		var xParent = this.getCoordParent(this.conf.x, num);
		var yParent = this.getCoordParent(this.conf.y, num);

		if(xParent == yParent) {
			getMapObjByDomObjId(xParent).delChild(this);
		} else {
			getMapObjByDomObjId(xParent).delChild(this);
			getMapObjByDomObjId(yParent).delChild(this);
		}

		// FIXME: Maybe the parent object is also a line. Then -1 is not correct
		//        But it is not coded to attach relative objects to lines. So it is no big
		//        deal to leave this as it is.
		if(num === -1) {
			this.conf.x = this.parseCoord(this.conf.x, 'x');
			this.conf.y = this.parseCoord(this.conf.y, 'y');
		} else {
			var old  = this.conf.x.split(',');
			old[num] = this.parseCoord(this.conf.x, 'x');
			this.conf.x = old.join(',');

			old  = this.conf.y.split(',');
			old[num] = this.parseCoord(this.conf.y, 'y');
			this.conf.y = old.join(',');
			old = null;
		}
	},

	// Transform the current coords to relative
	// coords to the given object
	makeRelativeCoords: function(oParent, num) {
		var xParent = this.getCoordParent(this.conf.x, num);
		var yParent = this.getCoordParent(this.conf.y, num);

		var x = num === -1 ? this.conf.x : this.conf.x.split(',')[num];
		var y = num === -1 ? this.conf.y : this.conf.y.split(',')[num];

		if(isRelativeCoord(x) && isRelativeCoord(y)) {
			// Skip this when already relative to the same object
		  if(xParent == oParent.conf.object_id
			  && yParent == oParent.conf.object_id)
				return;

			// If this object was attached to another parent before, remove the attachment
			if(xParent != oParent.conf.object_id)
				getMapObjByDomObjId(xParent).delChild(this);
			if(yParent != oParent.conf.object_id)
				getMapObjByDomObjId(yParent).delChild(this);
		}

		// Add this object to the new parent
		oParent.addChild(this);

		// FIXME: Maybe the parent object is also a line. Then -1 is not correct
		//        But it is not coded to attach relative objects to lines. So it is no big
		//        deal to leave this as it is.
		if(num === -1) {
			this.conf.x = this.getRelCoords(oParent, this.parseCoord(this.conf.x, 'x'), 'x', -1);
			this.conf.y = this.getRelCoords(oParent, this.parseCoord(this.conf.y, 'y'), 'y', -1);
		} else {
			var newX = this.getRelCoords(oParent, this.parseCoords(this.conf.x, 'x')[num], 'x', -1);
			var newY = this.getRelCoords(oParent, this.parseCoords(this.conf.y, 'y')[num], 'y', -1);

			var old  = this.conf.x.split(',');
			old[num] = newX;
			this.conf.x = old.join(',');

			old  = this.conf.y.split(',');
			old[num] = newY;
			this.conf.y = old.join(',');
		}
	},

	/**
	 * Returns the object id of the parent object
	 */
	getCoordParent: function(val, num) {
		var coord = num === -1 ? val.toString() : val.split(',')[num].toString();
		return coord.search('%') !== -1 ? coord.split('%')[0] : coord;
	},

	getRelCoords: function(refObj, val, dir, num) {
		var refPos = num === -1 ? refObj.conf[dir] : refObj.conf[dir].split(',')[num];
		var offset = parseInt(val) - parseInt(refObj.parseCoord(refPos, dir));
		var pre    = offset >= 0 ? '+' : '';
		val        = refObj.conf.object_id + '%' + pre + offset;
		refObj     = null;
		return val;
	},

	/**
	 * Calculates new coordinates for the object where the given parameter
	 * 'val' is the integer representing the current position of the object
	 * in absolute px coordinates. If the object position is related to
	 * another object this function detects it and transforms the abslute px
	 * coordinate to a relative coordinate and returns it.
	 *
	 * @author  Lars Michelsen <lars@vertical-visions.de>
	 */
	calcNewCoord: function(val, dir, num) {
		if(!isset(num))
			var num = -1;

		var oldVal = num === -1 ? this.conf[dir] : this.conf[dir].split(',')[num];
		// Check if the current value is an integer or a relative coord
		if(isset(oldVal) && isRelativeCoord(oldVal)) {
			// This must be an object id
			var objectId = null;
			if(oldVal.search('%') !== -1)
				objectId = oldVal.split('%')[0];
			else
				objectId = oldVal;

			// Only an object id. Get the coordinate and return it
			var refObj = getMapObjByDomObjId(objectId);
			// FIXME: Maybe the parent object is also a line. Then -1 is not correct
			if(refObj)
				val = this.getRelCoords(refObj, val, dir, -1);
			objectId = null;
		}
		oldVal = null;

		if(num === -1) {
			return val;
		} else {
			var old  = this.conf[dir].split(',');
			old[num] = val;
			return old.join(',');
		}
	},

	/**
	 * Used to gather all referenced parent object ids from the object
	 * configuration. Returns a object where the keys are the gathered
	 * parent object ids.
	 *
	 * @author  Lars Michelsen <lars@vertical-visions.de>
	 */
	getParentObjectIds: function() {
		var parentIds = {};
		var coords = (this.conf.x + ',' + this.conf.y).split(',');
		for(var i = 0, len = coords.length; i < len; i++)
			if(isRelativeCoord(coords[i]))
				if(coords[i].search('%') !== -1)
					parentIds[coords[i].split('%')[0]] = true;
				else
					parentIds[coords[i]] = true;
		coords = null;

		return parentIds;
	},

	/**
	 * This is used to add a child item to the object. Child items are
	 * gathered automatically by the frontend. Child positions depend
	 * on the related parent position on the map -> relative positioning.
	 *
	 * @author  Lars Michelsen <lars@vertical-visions.de>
	 */
	addChild: function(obj) {
		if(this.childs.indexOf(obj) === -1)
			this.childs.push(obj);
		obj = null;
	},

	delChild: function(obj) {
		this.childs.splice(this.childs.indexOf(obj), 1);
		obj = null;
	},

	highlight: function(show) {}
});
