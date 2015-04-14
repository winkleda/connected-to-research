(function(){

	var app = angular.module('publication', ['ui.bootstrap']);

	// directive for the filter items
	app.directive('filter', function(){
		return{
			restrict:'E',
			templateUrl:"tpl/filter.tpl.html"
		};
	});

	//directive for the publication items
	app.directive('publicationItem', function(){
		return{
			restrict:'E',
			templateUrl:'tpl/publication-item.tpl.html'
		};
	});

	//directives for the participation items
	app.directive('participationItem', function(){
		return{
			restrict:'E',
			templateUrl:'tpl/participation-item.tpl.html'
		};
	});

	//directive for research events and deadlines
	app.directive('researchEventsAndDeadlines', function(){
		return{
			restrict:'E',
			templateUrl:'tpl/research-events-deadlines.tpl.html'
		};
	});
	
	//directive for generating the user nav bar
	app.directive('userNavBar', function(){
		return{
			restrict: 'E',
			templateUrl:'tpl/nav-bar.tpl.html'
		};
	});

	//controller for the header bar
	app.controller('NavbarController', ['$http', function($http){
		var navbarCtrl = this;

		navbarCtrl.userInfo = [];
		$http.get("ajax/user-nav-bar-info.php").success(function(data){
			navbarCtrl.userInfo = data;
		});

		navbarCtrl.navBarPublicationItems = [];
		$http.get("ajax/nav-bar-publications.php").success(function(data){
			navbarCtrl.navBarPublicationItems = data;
		});

	}]);

	//main controller for the content
	app.controller('PublicationController',['$http', '$scope', '$log', '$interval', function($http, $scope, $log, $interval){
		var publicationCtrl = this;

		publicationCtrl.currentFilterType = 'recommended';

		publicationCtrl.selectedUsers = [];

		publicationCtrl.filter = [];
		$http.get("ajax/filter.php").success(function(data){
			publicationCtrl.filter = data;
		});

		publicationCtrl.publicationItems = [];
		
		$scope.publicationItemCall = function(type){
			publicationCtrl.currentFilterType = type;

			$http({
				method:'GET',
				url:'ajax/publication-items.php',
				params:{
					type:publicationCtrl.currentFilterType
				}
			}).success(function(data){
				publicationCtrl.publicationItems=data;
			});
		};

		$scope.publicationItemCall(publicationCtrl.currentFilterType);

		publicationCtrl.participationItems = [];
		$http.get("ajax/participation.php").success(function(data){
			publicationCtrl.participationItems = data;
		});
		
		publicationCtrl.researchEventsDeadlines = [];
		$http.get("ajax/research-events-deadlines.php").success(function(data){
			publicationCtrl.researchEventsDeadlines = data;
		});

		publicationCtrl.otherUsers = [];
		$http.get("scripts/get_users.php").success(function(data){
			publicationCtrl.otherUsers = data;
		});

		$scope.refreshCall = function(){
			$http.get("ajax/filter.php").success(function(data){
				publicationCtrl.filter = data;
			});

// commenting out refreshing the publication items for now, might not need this
//			$http.get("ajax/publication-items.php").success(function(data){
//				publicationCtrl.publicationItems = data;
//			});	

			$http.get("ajax/participation.php").success(function(data){
				publicationCtrl.participationItems = data;
			});
		
			$http.get("ajax/research-events-deadlines.php").success(function(data){
				publicationCtrl.researchEventsDeadlines = data;
			});
		};
		// this function will check to see what users have been selected and then 
		// call the share script with the selected user and article id as the 
		// params to be sent in the get request. 
		$scope.shareWithUsers = function(users, articleID){
			var userString = '';
			//$log.log(users);
			//$log.log(articleID);
			for (var index in users){
				if(users[index]){
					userString = userString + index + ',';
				}
			}
			if(userString.length != 0){
				//$log.log('current userString: ' + userString);
				userString = userString.substring(0, userString.length - 1);
				$http({
					method:'GET',
					url:'scripts/share_article.php',
					params:{
						user: userString,
						id: articleID
					}
				}).success(function(data){
					$scope.refreshCall();		
				});
			}
		};
		
		$scope.favoriteArticle = function(articleID){
			$http({
				method:'GET',
				url:'scripts/favorite_article.php',
				params:{
					id: articleID
				}
			}).success(function(data){
				$scope.refreshCall();
			});
		};		

		$interval(function(){$scope.refreshCall();}, 10000);
		
		$scope.getCitation = function(articleID){
			$scope.citationData = []
			$http({
				method:"GET",
				url:"scripts/generate_citation.php",
				params:{ id: articleID }
				}).success(function(data){
				$scope.citationData = data;
			});
		};
		
		$scope.addParticipationEvent = function(eventID){
			$http({
				method:"GET",
				url:"scripts/insert_events_and_deadlines.php",
				params:{ id: eventID }
			}).success(function(data){
				$scope.refreshCall();
			});
		};
		
	}]);
	
})();