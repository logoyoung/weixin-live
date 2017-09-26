!(function(){
	var GIFT = null;
	
	
	//----------------
	/* Define the number of leaves to be used in the animation */
	const NUMBER_OF_LEAVES = 50;

	/* 
	    Called when the "Falling Leaves" page is completely loaded.
	*/
	function start()
	{
	    /* Get a reference to the element that will contain the leaves */
	    var container = document.getElementById('leafContainer');
	    /* Fill the empty container with new leaves */
	    for (var i = 0; i < NUMBER_OF_LEAVES; i++) 
	    {
	        container.appendChild(createALeaf());
	    }
	}
	
	function stop(){
		var container = document.getElementById('leafContainer');
		container.innerHTML = '';
	}

	/*
	    Receives the lowest and highest values of a range and
	    returns a random integer that falls within that range.
	*/
	function randomInteger(low, high)
	{
	    return low + Math.floor(Math.random() * (high - low));
	}


	/*
	   Receives the lowest and highest values of a range and
	   returns a random float that falls within that range.
	*/
	function randomFloat(low, high)
	{
	    return low + Math.random() * (high - low);
	}


	/*
	    Receives a number and returns its CSS pixel value.
	*/
	function pixelValue(value)
	{
	    return value + 'px';
	}


	/*
	    Returns a duration value for the falling animation.
	*/

	function durationValue(value)
	{
	    return value + 's';
	}


	/*
	    Uses an img element to create each leaf. "Leaves.css" implements two spin 
	    animations for the leaves: clockwiseSpin and counterclockwiseSpinAndFlip. This
	    function determines which of these spin animations should be applied to each leaf.
	    
	*/
	function createALeaf()
	{
	    /* Start by creating a wrapper div, and an empty img element */
	    var leafDiv = document.createElement('div');
	    var image = document.createElement('img');
	    
	    /* Randomly choose a leaf image and assign it to the newly created element */
	    image.src = '../img/realLeaf' + '4' + '.png';
	    
	    leafDiv.style.top = "-50px";

	    /* Position the leaf at a random location along the screen */
	    leafDiv.style.left = pixelValue(randomInteger(0, 500));
	    
	    /* Randomly choose a spin animation */
	    var spinAnimationName = (Math.random() < 0.5) ? 'clockwiseSpin' : 'counterclockwiseSpinAndFlip';
	    
	    /* Set the -webkit-animation-name property with these values */
	    leafDiv.style.webkitAnimationName = 'fade, drop';
	    image.style.webkitAnimationName = spinAnimationName;
	    
	    /* Figure out a random duration for the fade and drop animations */
	    var fadeAndDropDuration = durationValue(randomFloat(5, 11));
	    
	    /* Figure out another random duration for the spin animation */
	    var spinDuration = durationValue(randomFloat(4, 8));
	    /* Set the -webkit-animation-duration property with these values */
	    leafDiv.style.webkitAnimationDuration = fadeAndDropDuration + ', ' + fadeAndDropDuration;

	    var leafDelay = durationValue(randomFloat(0, 5));
	    leafDiv.style.webkitAnimationDelay = leafDelay + ', ' + leafDelay;

	    image.style.webkitAnimationDuration = spinDuration;

	    // add the <img> to the <div>
	    leafDiv.appendChild(image);

	    /* Return this img element so it can be added to the document */
	    return leafDiv;
	}
	//----------------
	
	var boom = {
			init:function(){
				document.getElementById('boom').innerHTML = "<div class=\"gift-animation\">" +
						"<img style=\"width:6rem;height:6rem;\" src=\"../img/boom.gif\"/></div>";
			},
			clear:function(){
				document.getElementById('boom').innerHTML = '';
			},
			start:function(){
				this.init();
				//setTimeout(this.clear,5000);
			},
			
	};
	
	var dianzan = {
			star:[],
			init:function(){
				var that = this;
				var interval = setInterval(function(){
					that.up();
					if(that.star.length>20){
						var star = that.star.shift();
						star.remove();
					}
						
				},200);
			},
			up:function(){
				var dianzan = document.getElementById('dianzan');
				var star = this.createStar();
				dianzan.appendChild(star);
				var position = this.getPosition();
				setTimeout(function(){
					star.style.right = position.right;
					star.style.bottom = position.bottom;
					star.style.opacity = position.opacity;
				},100);
				this.star.push(star);
			},
			createStar:function(){
				var star = document.createElement('div');
				star.className = 'dianzan-animation star heart';
				star.className += ' ' + this.getColor();
				return star;
			},
			getColor:function(){
				var color = ['red','grren','yellow','blue'];
				var index = parseInt(Math.random()*color.length);
				return color[index];
			},
			getPosition:function(){
				var right = Math.random()*20 + 5 +'%';
				var bottom = Math.random()*30+50+'%';
				return {right:right,bottom:bottom,opacity:0};
			},
	}
	
	
	window.GIFT = {
			flower:{start:start,stop:stop},
			boom:boom,
			dianzan:dianzan,
	};
	
}())