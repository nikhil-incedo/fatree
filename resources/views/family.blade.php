<!DOCTYPE html>
<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous"> -->
<style>

body {
  text-align: center;
}

svg {
  margin-top: 32px;
  border: 1px solid #aaa;
}

.person rect {
  fill: #fff;
  stroke: steelblue;
  stroke-width: 1px;
}

.person {
  font: 14px sans-serif;
  cursor: pointer;
}

.link {
  fill: none;
  stroke: #ccc;
  stroke-width: 1.5px;
}

</style>
<body>
<script src="//cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>
<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
<script src="https://npmcdn.com/tether@1.2.4/dist/js/tether.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script>

var ancestorRoot, descendantRoot, spouseRoot;

var boxWidth = 150,
    boxHeight = 40,
    duration = 750; // duration of transitions in ms

// Setup zoom and pan
var zoom = d3.behavior.zoom()
  .scaleExtent([.1,1])
  .on('zoom', function(){
    svg.attr("transform", "translate(" + d3.event.translate + ") scale(" + d3.event.scale + ")");
  })
  // Offset so that first pan and zoom does not jump back to the origin
  .translate([400, 200]);

var svg = d3.select("body").append("svg")
  .attr('width', 1000)
  .attr('height', 500)
  .call(zoom)
  .append('g')

  // Left padding of tree so that the whole root node is on the screen.
  // TODO: find a better way
  .attr("transform", "translate(400,200)");

var ancestorsTree = d3.layout.tree()

  // Using nodeSize we are able to control
  // the separation between nodes. If we used
  // the size parameter instead then d3 would
  // calculate the separation dynamically to fill
  // the available space.
  .nodeSize([100, 200])

  // By default, cousins are drawn further apart than siblings.
  // By returning the same value in all cases, we draw cousins
  // the same distance apart as siblings.
  .separation(function(){
    return .5;
  })

  // Tell d3 what the child nodes are. Remember, we're drawing
  // a tree so the ancestors are child nodes.
  .children(function(person){

    // If the person is collapsed then tell d3
    // that they don't have any ancestors.
    if(person.collapsed){
      return;
    } else {
      return person._parents;
    }
  });

// Use a separate tree to display the descendants
var descendantsTree = d3.layout.tree()
  .nodeSize([100, 200])
  .separation(function(){
    return .5;
  })
  .children(function(person){
    if(person.collapsed){
      return;
    } else {
      return person._children;
    }
  });

// Use a separate tree to display the spouse
var spouseTree = d3.layout.tree()
  .nodeSize([200, 300])
  .separation(function(){
    return .5;
  })
  .children(function(person){
    if(person.collapsed){
      return;
    } else {
      return person._spouse;
    }
  });

d3.json('8gens.json', function(error, json){

  if(error) {
    return console.error(error);
  }

  // D3 modifies the objects by setting properties such as
  // coordinates, parent, and children. Thus the same node
  // node can't exist in two trees. But we need the root to
  // be in both so we create proxy nodes for the root only.
  ancestorRoot = rootProxy(json);
  descendantRoot = rootProxy(json);
  spouseRoot = rootProxy(json);

  // Start with only the first few generations showing
  ancestorRoot._parents.forEach(function(parents){
    parents._parents.forEach(collapse);
  });

  descendantRoot._children.forEach(collapse);

  spouseRoot._parents.forEach(collapse);

  drawAncestors(ancestorRoot);
  drawDescendants(descendantRoot);
  drawSpouse(spouseRoot);

});

function colorNode(name){
  //iterate through all the dom and get the DOM which has the data
  var node = d3.selectAll(".person")[0].filter(function(d){
    return d3.select(d).data()[0].name == name;
  });
  //for the matching node DOM set the fill to be red
  d3.selectAll(node).style("fill", "red");
}

function rootProxy(root){
  return {
    name: root.name,
    id: root.id,
    x0: 0,
    y0: 0,
    _children: root._children,
    _parents: root._parents,
    _spouse: root._spouse,
    collapsed: false
  };
}

function drawAncestors(source){
  draw(source, ancestorsTree, ancestorRoot, 'ancestor', 1);
}

function drawDescendants(source){
  draw(source, descendantsTree, descendantRoot, 'descendant', -1);
}

function drawSpouse(source){
  draw(source, spouseTree, spouseRoot, 'spouse', -2);
}

function draw(source, tree, root, displayClass, direction){


  var nodes = tree.nodes(root),
      links = tree.links(nodes);

  // Update links
  var link = svg.selectAll("path.link." + displayClass)

      // The function we are passing provides d3 with an id
      // so that it can track when data is being added and removed.
      // This is not necessary if the tree will only be drawn once
      // as in the basic example.
      .data(links, function(d){ return d.target.id; });

  // Add new links
  // Transition new links from the source's
  // old position to the links final position
  link.enter().append("path")
      .attr("class", "link " + displayClass)
      .attr("d", function(d) {
        if(direction == -2) {
          //var o = {x: 200, y:  1000};
          return transitionElbowspouce(5,10);
        }
        else {
          var o = {x: source.x0, y: direction * (source.y0 + boxWidth/2)};
        }
        return transitionElbow({source: o, target: o});
      });

  // Update the old links positions
  link.transition()
      .duration(duration)
      .attr("d", function(d){
        if(direction == -2) {
          return elbowspouse(d, direction);
        }
        else
          return elbow(d, direction);
      });

  // Remove any links we don't need anymore
  // if part of the tree was collapsed
  // Transition exit links from their current position
  // to the source's new position
  link.exit()
      .transition()
      .duration(duration)
      .attr("d", function(d) {
        if(direction == -2){
          var o = {x: 100, y:   -1};
          return transitionElbowspouce(5,10);
        }
        else {
          var o = {x: source.x, y: direction * (source.y + boxWidth/2)};
        }

        return transitionElbow({source: o, target: o});
      })
      .remove();

  // Update nodes
  var node = svg.selectAll("g.person." + displayClass)

      // The function we are passing provides d3 with an id
      // so that it can track when data is being added and removed.
      // This is not necessary if the tree will only be drawn once
      // as in the basic example.
      .data(nodes, function(person){ return person.id; });

  // Add any new nodes
  var nodeEnter = node.enter().append("g")
      .attr("class", "person " + displayClass)

      // Add new nodes at the right side of their child's box.
      // They will be transitioned into their proper position.
      .attr('transform', function(person){
        if(direction == -2 ){
            //var spouseDirection = 'translate(' + 0 + ',' + (-(source.x0 + boxWidth/2)) + ')';
            var spouseDirection = 'translate(' + 0 + ',' + (100) + ')';
           return spouseDirection;
        }else {
            return 'translate(' + (direction * (source.y0 + boxWidth/2)) + ',' + source.x0 + ')';
        }
      })
      .on('click', togglePerson).on("mouseover", function(d){
        //first make all the nodes/links black(reset).
        d3.selectAll(".person").style("fill", "black");
        // d3.selectAll(".link").style("stroke", "green");
        //color the node which is hovered.
        colorNode(d.name);
        //iterate over the imports which is the targets of the node(on which it is hovered) and color them.
        // console.log(d);
        // d.imports.forEach(function(name){
        //   colorNode(name);
        //   //color the link for a given source and target name.
        //   //colorLink(d.name, name);
        // });
      })  ;


  // Draw the rectangle person boxes.
  // Start new boxes with 0 size so that
  // we can transition them to their proper size.
  nodeEnter.append("rect")
      .attr({
        x: 0,
        y: 0,
        width: 0,
        height: 0
      });

  // Draw the person's name and position it inside the box
  nodeEnter.append("text")
      .attr("dx", 0)
      .attr("dy", 0)
      .attr("text-anchor", "start")
      .attr('class', 'name')
      .text(function(d) {
        return d.name;
      })
      .style('fill-opacity', 0);

  // Update the position of both old and new nodes
  var nodeUpdate = node.transition()
      .duration(duration)
      .attr("transform", function(d) {
        if(direction == -2){
          var  spouseDirection = "translate(" + 0 + "," + (100) + ")";
          return spouseDirection;
        }
        else
          return "translate(" + (direction * d.y) + "," + d.x + ")";
      });

  // Grow boxes to their proper size
  nodeUpdate.select('rect')
      .attr({
        x: -(boxWidth/2),
        y: -(boxHeight/2),
        width: boxWidth,
        height: boxHeight
      });

  // Move text to it's proper position
  nodeUpdate.select('text')
      .attr("dx", -(boxWidth/2) + 10)
      .style('fill-opacity', 1);

  // Remove nodes we aren't showing anymore
  var nodeExit = node.exit()
      .transition()
      .duration(duration)

      // Transition exit nodes to the source's position
      .attr("transform", function(d) {
        if(direction == -2 ){
          var spouseDirection = "translate(" + "0" + "," + (100)  + ")";
          return spouseDirection;
        }
        else{
          return "translate(" + (direction * (source.y + boxWidth/2)) + "," + source.x + ")";
        }
      })
      .remove();

  // Shrink boxes as we remove them
  nodeExit.select('rect')
      .attr({
        x: 0,
        y: 0,
        width: 0,
        height: 0
      });

  // Fade out the text as we remove it
  nodeExit.select('text')
      .style('fill-opacity', 0)
      .attr('dx', 0);

  // Stash the old positions for transition.
  nodes.forEach(function(person) {
    person.x0 = person.x;
    person.y0 = person.y;
  });

  colorNode("Isaac Warren");
}

/**
 * Update a person's state when they are clicked.
 */
function togglePerson(person){

  // Figure out of the root node was clicked. Remember
  // we have two proxy root nodes so we have handle both.
  if(person === ancestorRoot || person === descendantRoot){
    if(ancestorRoot.collapsed){
      ancestorRoot.collapsed = false;
      descendantRoot.collapsed = false;
    } else {
      collapse(ancestorRoot);
      collapse(descendantRoot);
    }
    drawDescendants(descendantRoot);
    drawAncestors(ancestorRoot);
  }

  // Non-root nodes
  else {

    if(person.collapsed){
      person.collapsed = false;
    } else {
      collapse(person);
    }

    // Figure out which tree the person belongs too.
    // Don't need to redraw when leaf nodes are
    // toggled because they don't expand or collapse,
    // therefore we don't account for them.
    if(person._children){
      drawDescendants(person);
      drawSpouse(person);
    } else if(person._parents){
      drawAncestors(person);
    }
  }
}

/**
 * Collapse person (hide their ancestors). We recursively
 * collapse the ancestors so that when the person is
 * expanded it will only reveal one generation. If we don't
 * recursively collapse the ancestors then when
 * the person is clicked on again to expand, all ancestors
 * that were previously showing will be shown again.
 * If you want that behavior then just remove the recursion
 * by removing the if block.
 */
function collapse(person){
  person.collapsed = true;
  if(person._parents){
    person._parents.forEach(collapse);
  }
  if(person._children){
    person._children.forEach(collapse);
  }
}

/**
 * Custom path function that creates straight connecting
 * lines. Calculate start and end position of links.
 * Instead of drawing to the center of the node,
 * draw to the border of the person profile box.
 * That way drawing order doesn't matter. In other
 * words, if we draw to the center of the node
 * then we have to draw the links first and the
 * draw the boxes on top of them.
 */
function elbow(d, direction) {
  var sourceX = d.source.x,
      sourceY = d.source.y + (boxWidth / 2),
      targetX = d.target.x,
      targetY = d.target.y - (boxWidth / 2);

  return "M" + (direction * sourceY) + "," + sourceX
    + "H" + (direction * (sourceY + (targetY-sourceY)/2))
    + "V" + targetX
    + "H" + (direction * targetY);
}

function elbowspouse(d, direction) {


  return "M" + "0,20"
    + "H" +"0"
    + "V" +"100"
    + "H" + "0";
}

/**
 * Use a different elbow function for enter
 * and exit nodes. This is necessary because
 * the function above assumes that the nodes
 * are stationary along the x axis.
 */
function transitionElbow(d){
  return "M" + d.source.y + "," + d.source.x
    + "H" + d.source.y
    + "V" + d.source.x
    + "H" + d.source.y;
}
function transitionElbowspouce(){
  return "M" + 0 + "," + 0
    + "H" + 0
    + "V" + 0
    + "H" + 0
}

</script>
<div>
Details of this family
</div>
<div class="col-lg-12" style="margin-bottom:100px;">
  <div class="col-lg-10 col-lg-offset-1">

  <!-- Nav tabs -->
  <ul class="nav nav-tabs">
    <li class="active">
        <a href="#propertyDetails" data-toggle="tab">
            Property Details</a>
    </li>
    <li>
        <a href="#educationDetails" data-toggle="tab">
            Education Details</a>
    </li>
    <li>
        <a href="#medicalDetails" data-toggle="tab">
            Medical Details</a>
    </li>
   <!--  <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Messages</a></li>
    <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Settings</a></li> -->
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div class="tab-pane active" id="propertyDetails">Property Details
          <table border="1" cellpadding="0" cellspacing="2" width="100%">
          <tr>
            <th align="left" style="padding-left: 20px;">S No.</th>
            <th align="left" style="padding-left: 20px;">Name</th>
            <th align="left" style="padding-left: 20px;">Amount</th>
            <th align="left" style="padding-left: 20px;"># Banks</th>
          </tr>
          <?php
            $i=0;
            foreach($userDetails AS $user) {
            // }?>
            <tr>
                <td align="left" style="padding-left: 20px;"><?php echo ++$i;?></td>
                <td align="left" style="padding-left: 20px;"><?php echo $user->first_name . ' ' .$user->last_name;?></td>
                <td align="left" style="padding-left: 20px;"><?php echo number_format($user->Property->estimate, 2);?></td>
                <td align="left" style="padding-left: 20px;"><?php echo $user->Property->bank;?></td>
            </tr>
            <?php }?>
          </table>
    </div>
    <div class="tab-pane" id="educationDetails">Education History
        <table border="1" cellpadding="0" cellspacing="2" width="100%">
          <tr>
            <th align="left" style="padding-left: 20px;">S No.</th>
            <th align="left" style="padding-left: 20px;">Name</th>
            <th align="left" style="padding-left: 20px;">Education</th>
          </tr>
          <?php
            $i=0;
            foreach($userDetails AS $user) {
            ?>
            <tr>
                <td align="left" style="padding-left: 20px;"><?php echo ++$i;?></td>
                <td align="left" style="padding-left: 20px;"><?php echo $user->first_name . ' ' .$user->last_name;?></td>
                <td align="left" style="padding-left: 20px;"><?php echo $user->Property->education;?></td>
            </tr>
            <?php }?>
          </table>
    </div>
    <div class="tab-pane" id="medicalDetails">Medical History -- In Progress</div>
    <!-- <div role="tabpanel" class="tab-pane" id="messages">m</div>
    <div role="tabpanel" class="tab-pane" id="settings">as</div> -->
  </div>

</div>
</div>
<script type="text/javascript">
  $('#myTabs a').click(function (e) {
  e.preventDefault()
  $(this).tab('show')
})
</script>
