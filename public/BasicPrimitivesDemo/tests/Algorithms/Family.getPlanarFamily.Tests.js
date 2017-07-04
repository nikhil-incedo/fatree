QUnit.module('Algorithms - Family class, getPlanarFamily function tests');

QUnit.test("primitives.common.family.getPlanarFamily - Function eliminates some relations in family, so they don;t cross each other.", function (assert) {

	function getLevels(family) {
		var levels = [];
		family.loopLevels(this, true, function (itemid, item, level) {
			var newItem = { id: itemid };
			var children = [];
			family.loopChildren(this, itemid, function (itemid, item, levelIndex) {
				if (levelIndex > 0) {
					return family.BREAK;
				}
				children.push(itemid);
			});
			if (children.length > 0) {
				newItem.children = children;
			}
			levels.push(newItem);
		});
		return levels;
	}

	var items = [
		{ id: 1 },
		{ id: 2 },
		{ id: 3 },
		{ id: 4 },
		{ id: 5, parents: [1] },
		{ id: 6, parents: [1] },
		{ id: 7, parents: [2, 3] },
		{ id: 8, parents: [4] }
	];

	var family = primitives.common.family();
	for (var index = 0; index < items.length; index += 1) {
		var item = items[index];
		family.add(item.parents, item.id, item);
	}

	var levels = [[1, 2, 3, 4], [5, 6, 7, 8]];

	var treeLevels = primitives.common.TreeLevels();
	for (var levelIndex = 0, levelLen = levels.length; levelIndex < levelLen; levelIndex += 1) {
		var level = levels[levelIndex];
		for (var index = 0, len = level.length; index < len; index += 1) {
			treeLevels.addItem(levelIndex, level[index], {});
		}
	}

	var planarFamily = family.getPlanarFamily(treeLevels);

	var expectedResult = getLevels(family);
	var result = getLevels(planarFamily);
	var isValid = JSON.stringify(result) == JSON.stringify(expectedResult) && planarFamily.validate();
	assert.ok(isValid, "Function should return the same family structure without changes: " + JSON.stringify(expectedResult) + (isValid ? "" : " but returned " + JSON.stringify(result)));

	// Test elimination of edge in two intersecting hierarchies
	var items = [
		{ id: 1 },
		{ id: 2 },
		{ id: 3, parents: [1] },
		{ id: 4, parents: [2] },
		{ id: 5, parents: [1] },
		{ id: 6, parents: [2] }
	];

	var family = primitives.common.family();
	for (var index = 0; index < items.length; index += 1) {
		var item = items[index];
		family.add(item.parents, item.id, item);
	}

	var levels = [[1, 2], [3, 4, 5, 6]];



	var treeLevels = primitives.common.TreeLevels();
	for (var levelIndex = 0, levelLen = levels.length; levelIndex < levelLen; levelIndex += 1) {
		var level = levels[levelIndex];
		for (var index = 0, len = level.length; index < len; index += 1) {
			treeLevels.addItem(levelIndex, level[index], {});
		}
	}

	var planarFamily = family.getPlanarFamily(treeLevels);



	var expectedResult = [{ "id": "1", "children": ["3", "5"] }, { "id": "2", "children": ["6"] }, { "id": "3" }, { "id": "5" }, { "id": "6" }];
	var result = getLevels(planarFamily);
	var isValid = JSON.stringify(result) == JSON.stringify(expectedResult) && planarFamily.validate();
	assert.ok(isValid, "Function should break edge 1-5: " + JSON.stringify(expectedResult) + (isValid ? "" : " but returned " + JSON.stringify(result)));


	// Test elimination of edge in two intersecting hierarchies
	var items = [
		{ id: 1 },
		{ id: 2 },
		{ id: 3, parents: [1] },
		{ id: 4, parents: [2] },
		{ id: 5, parents: [2] },
		{ id: 6, parents: [1] },
		{ id: 7, parents: [2] }
	];

	var family = primitives.common.family();
	for (var index = 0; index < items.length; index += 1) {
		var item = items[index];
		family.add(item.parents, item.id, item);
	}

	var levels = [[1, 2], [3, 4, 5, 6, 7]];



	var treeLevels = primitives.common.TreeLevels();
	for (var levelIndex = 0, levelLen = levels.length; levelIndex < levelLen; levelIndex += 1) {
		var level = levels[levelIndex];
		for (var index = 0, len = level.length; index < len; index += 1) {
			treeLevels.addItem(levelIndex, level[index], {});
		}
	}

	var planarFamily = family.getPlanarFamily(treeLevels);



	var expectedResult = [{ "id": "1", "children": ["3"] }, { "id": "2", "children": ["4", "5", "7"] }, { "id": "3" }, { "id": "4" }, { "id": "5" }, { "id": "7" }];
	var result = getLevels(planarFamily);
	var isValid = JSON.stringify(result) == JSON.stringify(expectedResult) && planarFamily.validate();
	assert.ok(isValid, "Function should break edge 1-6 because it cross 2 other relations: " + JSON.stringify(expectedResult) + (isValid ? "" : " but returned " + JSON.stringify(result)));

	// Test elimination of edge in two intersecting hierarchies
	var items = [
		{ id: 1 },
		{ id: 2 },
		{ id: 3 },
		{ id: 4 },
		{ id: 5, parents: [1, 2, 3] },
		{ id: 6, parents: [2, 3, 4] }
	];

	var family = primitives.common.family();
	for (var index = 0; index < items.length; index += 1) {
		var item = items[index];
		family.add(item.parents, item.id, item);
	}

	var levels = [[1, 2, 3, 4], [5, 6]];



	var treeLevels = primitives.common.TreeLevels();
	for (var levelIndex = 0, levelLen = levels.length; levelIndex < levelLen; levelIndex += 1) {
		var level = levels[levelIndex];
		for (var index = 0, len = level.length; index < len; index += 1) {
			treeLevels.addItem(levelIndex, level[index], {});
		}
	}

	var planarFamily = family.getPlanarFamily(treeLevels);



	var expectedResult = [{ "id": "1", "children": ["5"] }, { "id": "2", "children": ["5", "6"] }, { "id": "4", "children": ["6"] }, { "id": "3", "children": ["6"] }, { "id": "5" }, { "id": "6" }];
	var result = getLevels(planarFamily);
	var isValid = JSON.stringify(result) == JSON.stringify(expectedResult) && planarFamily.validate();
	assert.ok(isValid, "Function should break edge 3-5: " + JSON.stringify(expectedResult) + (isValid ? "" : " but returned " + JSON.stringify(result)));

	// Test elimination of edge in two intersecting hierarchies
	var items = [
		{ id: 1 },
		{ id: 2 },
		{ id: 3 },
		{ id: 4 },
		{ id: 5, parents: [1, 2, 3] },
		{ id: 6, parents: [1, 3, 4] }
	];

	var family = primitives.common.family();
	for (var index = 0; index < items.length; index += 1) {
		var item = items[index];
		family.add(item.parents, item.id, item);
	}

	var levels = [[1, 2, 3, 4], [5, 6]];



	var treeLevels = primitives.common.TreeLevels();
	for (var levelIndex = 0, levelLen = levels.length; levelIndex < levelLen; levelIndex += 1) {
		var level = levels[levelIndex];
		for (var index = 0, len = level.length; index < len; index += 1) {
			treeLevels.addItem(levelIndex, level[index], {});
		}
	}

	var planarFamily = family.getPlanarFamily(treeLevels);



	var expectedResult = [{ "id": "1", "children": ["5"] }, { "id": "3", "children": ["5", "6"] }, { "id": "2", "children": ["5"] }, { "id": "4", "children": ["6"] }, { "id": "5" }, { "id": "6" }];
	var result = getLevels(planarFamily);
	var isValid = JSON.stringify(result) == JSON.stringify(expectedResult) && planarFamily.validate();
	assert.ok(isValid, "Function should break edge 1-6 because it crosses 2 relations: " + JSON.stringify(expectedResult) + (isValid ? "" : " but returned " + JSON.stringify(result)));

	// Test elimination of edge should create orphant in the second level of children
	var items = [
		{ id: 1 },
		{ id: 2 },
		{ id: 3 },
		{ id: 4 },
		{ id: 5, parents: [4] },
		{ id: 6, parents: [1] },
		{ id: 7, parents: [2] },
		{ id: 8, parents: [3] }
	];

	var family = primitives.common.family();
	for (var index = 0; index < items.length; index += 1) {
		var item = items[index];
		family.add(item.parents, item.id, item);
	}

	var levels = [[1, 2, 3, 4], [5, 6, 7, 8]];



	var treeLevels = primitives.common.TreeLevels();
	for (var levelIndex = 0, levelLen = levels.length; levelIndex < levelLen; levelIndex += 1) {
		var level = levels[levelIndex];
		for (var index = 0, len = level.length; index < len; index += 1) {
			treeLevels.addItem(levelIndex, level[index], {});
		}
	}

	var planarFamily = family.getPlanarFamily(treeLevels);



	var expectedResult = [{ "id": "1", "children": ["6"] }, { "id": "2", "children": ["7"] }, { "id": "3", "children": ["8"] }, { "id": "6" }, { "id": "7" }, { "id": "8" }];
	var result = getLevels(planarFamily);
	var isValid = JSON.stringify(result) == JSON.stringify(expectedResult) && planarFamily.validate();
	assert.ok(isValid, "Function should make item 5 orphant: " + JSON.stringify(expectedResult) + (isValid ? "" : " but returned " + JSON.stringify(result)));
});