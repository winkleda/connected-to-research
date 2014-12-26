(function(){

	var app = angular.module('publication', []);

	app.directive('filter', function(){
		return{
			restrict:'E',
			templateUrl:"tpl/filter.tpl.html"
		};
	});
	
	app.controller('PublicationController',['$http', function($http){
		var publicationCtrl = this;
		publicationCtrl.filter = [];
		$http.get("test/filter.json").success(function(data){
			publicationCtrl.filter = data;
		});
	
	}]);
	
})();
