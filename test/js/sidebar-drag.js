var assert = require('assert'),
	fs = require('fs'),
	path = require('path');

describe('Sidebar element drag', function () {
	it('uses the helper belonging to the current drag', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../scripts/upfront/behaviors/dragdrop.js'), 'utf8'),
			updateStart = source.indexOf('update_vars: function'),
			updateEnd = source.indexOf('\n\treset:', updateStart),
			updateSource = source.slice(updateStart, updateEnd);

		assert.notEqual(updateSource.indexOf('this.ui && this.ui.helper ? this.ui.helper : this.$me'), -1);
		assert.equal(updateSource.indexOf("$('.ui-draggable-dragging')"), -1);
	});

	it('notifies sidebar cleanup before the stop callback can replace the shadow element', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../scripts/upfront/modern-ui-draggable.js'), 'utf8'),
			stopStart = source.indexOf('Draggable.prototype.stop = function'),
			stopEnd = source.indexOf('Draggable.prototype.setOptions', stopStart),
			stopSource = source.slice(stopStart, stopEnd),
			dragstop = stopSource.indexOf("this.triggerDragEvent('dragstop', event, ui)"),
			stopCallback = stopSource.indexOf('this.options.stop.call(this.element, event, ui)');

		assert.ok(dragstop !== -1 && dragstop < stopCallback);
	});

	it('converts element resize handles to selectors before calling InteractJS', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../scripts/upfront/modern-ui-resizable.js'), 'utf8'),
			selectorStart = source.indexOf('Resizable.prototype.getHandleSelector'),
			selectorEnd = source.indexOf('Resizable.prototype.ensureHandles', selectorStart),
			selectorSource = source.slice(selectorStart, selectorEnd);

		assert.notEqual(selectorSource.indexOf('element.nodeType === 1'), -1);
		assert.notEqual(selectorSource.indexOf("return '[' + HANDLE_ATTRIBUTE"), -1);
		assert.equal(selectorSource.indexOf('return handles[direction]'), -1);
	});

	it('derives fullwidth grid columns from the rendered inner grid width', function () {
		var gridSource = fs.readFileSync(path.join(__dirname, '../../scripts/upfront/behaviors/grid-editor.js'), 'utf8'),
			layoutSource = fs.readFileSync(path.join(__dirname, '../../scripts/upfront/behaviors/layout-editor.js'), 'utf8'),
			columnSizeStart = gridSource.indexOf('get_column_size: function'),
			columnSizeEnd = gridSource.indexOf('/**', columnSizeStart),
			columnSizeSource = gridSource.slice(columnSizeStart, columnSizeEnd);

		assert.notEqual(columnSizeSource.indexOf("hasClass('upfront-grid-layout-fullwidth')"), -1);
		assert.notEqual(columnSizeSource.indexOf('$layout.innerWidth() / ed.grid.size'), -1);
		assert.notEqual(gridSource.indexOf('ed.col_size = ed.get_column_size($grid_layout)'), -1);
		assert.notEqual(layoutSource.indexOf('grid_ed.get_column_size($grid_layout)'), -1);
	});
});