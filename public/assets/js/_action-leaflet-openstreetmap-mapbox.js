let position_me = [46.573795318603, 3.2033939361572];
let	map = L.map('map').setView(position_me, 11);
let popupCollection = [];

let wpMapbox = [];
wpMapbox.push(	
    wpMapbox['wp0'] = Object.keys(L.latLng(position_me)).map((key) => L.latLng(position_me)[key] )
)
let waypoints = [position_me];

L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
    attribution: 'Map data &copy; <a href="https://osm.org/copyright">OpenStreetMap</a> ODbL - rendu <a href="https://www.openstreetmap.fr/">OSM France</a>',
    minZoom: 1,
    maxZoom: 20
}).addTo(map);
map.scrollWheelZoom.disable();
new L.marker(position_me).addTo(map);

// On dessine les limites de zone en appelant le fichier .json
$.ajax({
    type: 'get',
    url: `/assets/js/france-geoJson2.json`,
    success :  function(geojson){
        let geoJsonLayer = L.geoJSON(geojson, {
            style: {
                    "color": "#839c49",
                    "opacity": 1,
                    "fillColor": "#839c49",
                    "fillOpacity": 0.5,
                    "bubblingMouseEvents": false // permet de dire que la tuille "Carte" et "Polygone" n'a pas le même evenement de souri
            }
        }).addTo(map);
        geoJSON = L.geoJSON(geojson)
    }
})

// On itère sur tous les utilisateurs afin de marquer ses positions sur la carte
var js_marker = $('.js-marker');
$.each(js_marker, function( index, item ) {
    let point = [item.dataset.lat, item.dataset.lng];
    
    let popup = new L.popup({
        autoClose: false,
        closeOnEscapeKey: false,
        closeOnClick: false,
        closeButton: false,
        className: 'marker',
        maxWidth: 400
    })
           
    let nbrOrderNotDelivred = item.dataset.order;
    // On affiche le popup, seulement si l'utilisateur a au moin une commande
    if (nbrOrderNotDelivred > 0) {
        // On agrandit le zone à chaque fois qu'on a des nouveaux marqueurs et après on ajuste le centrage
        waypoints.push(point);
        map.fitBounds(waypoints);

        // On fait +1, car wpMapbox a déjà une valeur de key 0
        key = index +1;
        index = 'wp'+key;
        wpMapbox.push(wpMapbox[index] = Object.keys(L.latLng(point)).map((key) => L.latLng(point)[key] ));	

        popup.setLatLng(point)
            .setContent("("+nbrOrderNotDelivred+")")
            .openOn(map)
        ;
        
        // On gère les évènements
        popup.getElement().addEventListener('click', function () {
            popup.getElement().classList.add('is-expanded');
            popup.setContent(`<p> <span class="user"> ${item.dataset.user} </span> <br> ${item.dataset.address} <br> Code postal : ${item.dataset.postalcode} <br>  ${L.latLng(point)} </p>`);
            popup.setContent(item.innerHtml);
            popup.update();
        });
        popup.getElement().addEventListener('mouseout', function () {
            popup.setContent("("+nbrOrderNotDelivred+")");
            popup.getElement().classList.remove('is-expanded');
            popup.update()
        });
        item.addEventListener('mouseover', function () {
            popup.getElement().classList.add('is-active');
        })
        item.addEventListener('mouseout', function () {
            popup.getElement().classList.remove('is-active'); 
        })
        popupCollection.push(popup);
    }
});

const svgpin_Icon2 = L.icon({
    iconUrl: "/assets/images/map-location-icon-red.png",
    iconSize: [24, 24],
    iconAnchor: [12, 24],
    popupAnchor: [0, -22]
});

var control = L.Routing.control({
    geocoder: L.Control.Geocoder.nominatim(),
    waypoints: waypoints,
    show: false,
    showAlternatives: false, 
    lineOptions:   {color: 'black', opacity: 0.15, weight: 9},
    router: new L.Routing.osrmv1({
        language: 'fr',
        profile: 'car'
    }),
    createMarker: function(i, wp, nWps) {
        switch (i) {
            case 0:
                return L.marker(wp.latLng, {
                    draggable: false
                }).bindPopup("<b> Point de départ </b>");
            case nWps - 1:
                return L.marker(wp.latLng, {
                    icon: svgpin_Icon2,
                    draggable: false
                }).bindPopup(`<b> Point d'arrivée </b>`).openPopup();
        }
    }
});
control.addTo(map);


var allDistance = [];
var destination = [
    [0+'et'+0 ]
];
var tabBestDist = [];
var newBestDist = 100000000;

function getRoutSummary(i, j) {
    if (errorReq) {
        $('#modalLoading').modal('hide');
        $('#modalError').modal('show');
        return;
    }

    const mapbox_api = $('div[data-mbapi]').data('mbapi');
    var url = 'https://api.mapbox.com/directions/v5/mapbox/driving/' +
            coords[i][1] + ',' + coords[i][0] + ';' +
            coords[j][1] + ',' + coords[j][0] +
            '?geometries=geojson&access_token='+mapbox_api;
        
    $.ajax({
        url: url,
        async: false,
        method: 'get',
        dataType: 'json',
        success: function(response){
            var distance = response.routes[0].distance;
            allDistance.push(allDistance[i + ' - ' + j ] = distance);
            // console.log('Distance entre ' + i + ' et ' + j + ' est ' +  Math.round(distance));
        },
        error:  function () {
            errorReq = true;
       }
    });
}

var destinationRoutes = [0] ;
var storyMinDist = [0];
var storyIndexMinDist = [];
var errorReq = false;

function getAllRoutes(){
    // Start script
    $('#modalLoading').modal('show');
    $('#modalLoading').addClass('modal-dialog-centered');
    $('.content-loading-modal .spinner-block').html('<div class="spinner-border text-success"></div>');
    

    // On parcours toutes les waypoints pour obtenir une matrice
    coords = wpMapbox;
    for (var i = 0; i < coords.length; i++){       
        for (var j = 0; j < coords.length; j++){
            getRoutSummary(i, j);
        }
    }

    var nbrMatrixRow = wpMapbox.length;
    var nextRow = 0;
    var nextBestMinDist = 0;
    var nextIndex = 0;
    var nextIndexPlusOne = 0;
    var nextStartRowMatrice = 0;
    var nextEndRowMatrice = 0;    

    for (let i = 0; i < (nbrMatrixRow-1); i++) {
        if (i == 0) {
            // On trie maintenant la matrice obtenue, pour avoir le chemin le plus court
            var oneRow = allDistance.slice(0, nbrMatrixRow);
            // console.log('oneRow', oneRow)

            var bestMinDist = Math.min.apply(Math, oneRow.filter(Boolean));
            
            storyMinDist.push(bestMinDist);
            storyIndexMinDist.push(allDistance.indexOf(bestMinDist))
            // console.log('min', bestMinDist)

            // Get index
            var index = oneRow.indexOf(bestMinDist);
            var indexPlusOne = index + 1;
            // console.log('index', index)
            // console.log('indexPlusOne', indexPlusOne)

            // +1 parce que, l'indice commence par 0
            var startRowMatrice = (index * nbrMatrixRow);
            var endRowMatrice = (indexPlusOne * nbrMatrixRow) ;
            // console.log('startRowMatrice', startRowMatrice)
            // console.log('endRowMatrice', endRowMatrice)
            
            nextRow = allDistance.slice(startRowMatrice, endRowMatrice);
            // console.log('nextRow', nextRow)
            destinationRoutes.push(index);
            
        } else {
            // console.log('nextRow'+i, nextRow)
            nextBestMinDist = Math.min.apply(Math, nextRow.filter((item) => checkNotInDestination(item, nextRow)));
            storyMinDist.push(nextBestMinDist);
            storyIndexMinDist.push(allDistance.indexOf(nextBestMinDist))

            // console.log('bestMin'+1, nextBestMinDist)

            nextIndex = nextRow.indexOf(nextBestMinDist);
            // console.log('nextIndex'+1, nextIndex)

            nextIndexPlusOne = nextIndex + 1;
            nextStartRowMatrice = (nextIndex * nbrMatrixRow);
            nextEndRowMatrice = (nextIndexPlusOne * nbrMatrixRow);
            
            nextRow = allDistance.slice(nextStartRowMatrice, nextEndRowMatrice);
            destinationRoutes.push(nextIndex);
        }
    }
    // console.log('destinationRoutes', destinationRoutes);

    shortestPath = [];
    destinationRoutes.forEach((element, index) => {
        shortestPath.push(
            // Waypoints tilisé pour L.Routing.control
            // wpMapbox[element]

            //Waypoints utilisé pour L.Routing.osrmv1
            new L.Routing.Waypoint(L.latLng(wpMapbox[element]))
        );
    });

    // console.log('sdfsdf', shortestPath)
    
    drawShortestPath(shortestPath);

    // End script
    $('#modalLoading').modal('hide');
    map.removeControl(control);

}

/**
 * **** Callback
 * Peremt de filtrer les valeur à ne pas prendre
 */
function checkNotInDestination(item, nextRow){
    var values = storyMinDist;

    // console.error('storyMinDist', storyMinDist)
    // console.error('storyIndexMinDist', storyIndexMinDist)
    // console.error('item', item)
    // console.error(allDistance.indexOf(item))
    // console.error(storyIndexMinDist.includes(allDistance.indexOf(item)))


    if (  (storyMinDist.includes(item) && storyIndexMinDist.includes(allDistance.indexOf(item)) ) || destinationRoutes.includes(nextRow.indexOf(item))  ) {
        values.push(item);
    }
    // console.error('values tsy raisina', values)
    // // On exclus 0 est tous les valueurs dans le tab values
    return !values.includes(item);

    // return !storyMinDist.includes(item);
}
function drawShortestPath(waypoints) {
    var control = L.Routing.control({
            geocoder: L.Control.Geocoder.nominatim(),
            waypoints: waypoints,
            show: false,
            altLineOptions: {
                styles: [{
                    color: 'blue',
                    opacity: 1,
                    weight: 1
                }]
            },
            router: new L.Routing.osrmv1({
                language: 'fr',
                profile: 'driving'
            }),
            createMarker: function(i, wp, nWps) {
                switch (i) {
                    case 0:
                        return L.marker(wp.latLng, {
                            draggable: false
                        }).bindPopup("<b> Point de départ </b>");
                    case nWps - 1:
                        return L.marker(wp.latLng, {
                            icon: svgpin_Icon2,
                            draggable: false
                        }).bindPopup(`<b> Point d'arrivée </b>`).openPopup();
                    default:
                        return L.marker(wp.latLng, {
                            icon: svgpin_Icon2,
                            draggable: false
                        }).bindPopup(`<b> n°${i} </b>`).openPopup();
                        
                }
            }
        });
        control.addTo(map);

        control.on('routesfound', function (e) {
            L.Routing.line(e.routes[0],{
                styles : [
                    {
                        color : 'blue',
                        weight : '10'
                    }
                ]
            })
        });



    // let routeUs = L.Routing.osrmv1(
    // 	{
    // 		// serviceUrl: 'http://router.project-osrm.org/route/v1',
    // 		language: 'fr',
    // 		profile: 'driving',
    // 	}
    // );
    // routeUs.route(
    // 	waypoints, 
    // 	function andrana(err, routes) {
    // 		if(!err)
    // 		{
    // 			L.Routing.line(routes[0],{
    // 				styles : [
    // 					{
    // 						color : 'blue',
    // 						weight : '10'
    // 					}
    // 				]
    // 			})
    // 			.addTo(map);
    // 		}
    // 	}
    // );
    // L.Routing.Itinerary({
    // 	pointMarkerStyle: {
    // 		radius: 5,
    // 		color: 'green',
    // 		fillColor: 'white',
    // 		opacity: 1,
    // 		fillOpacity: 0.7
    // 	}
    // })

    // Permet de cacher les popups
    // popupCollection.forEach(popup => {
    // 	popup.remove()
    // });
}
