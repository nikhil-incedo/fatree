QUnit.module('Algorithms - Fibonacci Heap');

QUnit.test("primitives.common.FibonacciHeap -  Closure based priority queue structure based on fibonacci heap algorithm.", function (assert) {
	var items = [
		[10, 1, "First"],
		[1, 10, "Moscow"],
		[2, 20, "Piter"],
		[3, 30, "Toronto"],
		[4, 40, "NY"],
		[5, 50, "Seoul"],
		[6, 60, "Maple"],
		[7, 70, "Vaughan"],
		[8, 80, "Redmond"]
	];

	var queue = primitives.common.FibonacciHeap(false);
	for (var index = 0, len = items.length; index < len; index += 1) {
		var item = items[index];
		queue.add(item[0], item[1], item[2]);
	}

	var result = [];
	var item = null;
	while ((item = queue.extractRoot()) != null) {
		result.push([item.key, item.priority, item.item]);
		queue.validate();
	}

	var expectedItems = [
		[10, 1, "First"],
		[1, 10, "Moscow"],
		[2, 20, "Piter"],
		[3, 30, "Toronto"],
		[4, 40, "NY"],
		[5, 50, "Seoul"],
		[6, 60, "Maple"],
		[7, 70, "Vaughan"],
		[8, 80, "Redmond"]
	];
	assert.ok(JSON.stringify(result) == JSON.stringify(expectedItems), "Structure should return sorted items: " + JSON.stringify(result));


	var items = [
	[10, 1, "First"],
	[1, 10, "Moscow"],
	[2, 20, "Piter"],
	[3, 30, "Toronto"],
	[4, 40, "NY"],
	[5, 50, "Seoul"],
	[6, 60, "Maple"],
	[7, 70, "Vaughan"],
	[8, 80, "Redmond"]
	];

	var queue = primitives.common.FibonacciHeap(false);
	for (var index = 0, len = items.length; index < len; index += 1) {
		var item = items[index];
		queue.add(item[0], item[1], item[2]);
	}

	queue.extractRoot()
	queue.validate();

	queue.setPriority(8, 1);
	queue.validate();

	var result = [];
	var item = null;
	while ((item = queue.extractRoot()) != null) {
		result.push([item.key, item.priority, item.item]);
		queue.validate();
	}

	var expectedItems = [
		[8, 1, "Redmond"],
		[1, 10, "Moscow"],
		[2, 20, "Piter"],
		[3, 30, "Toronto"],
		[4, 40, "NY"],
		[5, 50, "Seoul"],
		[6, 60, "Maple"],
		[7, 70, "Vaughan"]
	];
	assert.ok(JSON.stringify(result) == JSON.stringify(expectedItems), "Structure should return item #1 first: " + JSON.stringify(result));

});
