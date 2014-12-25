(function(){

	var app = angular.module('store', []);

	app.controller('StoreController', function(){
		this.products = gems;
	});

	var gems = [
	{
		name: 'Dodecahedron',
		price: 2.95,
		description: ' . . . .',
		images:[
		{
			full:'img/blue-gem.jpg',
			thumb:'img/gem.jpg',
		},
		{
			full:'img/gem.jpg',
			thumb:'img/blue-gem.jpg',
		}
		],
		reviews: [
		{
			stars: 5,
			body: "meh its alright i just cant count",
			author: "me@me.me",
		},
		{
			stars: 1,
			body: "this is awesome im on the internet",
			author: "tim@tim.tim"
		}
		],
		canPurchase: true,
	},
	{
		name: "Pentagonal Gem",
		price: 5.95,
		description: " something that i dont know",
		images:[
		{
			full:'img/gem.jpg',
			thumb:'img/gem.jpg',
		},
		{
			full:'img/gem.jpg',
			thumb:'img/blue-gem.jpg',
		}
		],
		reviews: [
		{
			stars: 5,
			body: "meh its alright i just cant count",
			author: "me@me.me",
		},
		{
			stars: 1,
			body: "this is awesome im on the internet",
			author: "tim@tim.tim"
		}
		],
		canPurchase: false,
	},
	];

	app.controller("PanelController", function(){
		this.tab = 1;
		
		this.selectTab = function(setTab){
			this.tab = setTab;
		};
		this.isSelected = function(checkTab){
			return this.tab === checkTab;
		};
	});
	
	app.controller("ReviewController", function(){
		this.review={};
		
		this.addReview= function(product){
			product.reviews.push(this.review);
			this.review = {}
		};
	});
	
})();