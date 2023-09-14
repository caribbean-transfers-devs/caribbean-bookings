const input = document.getElementById('bookingFromForm');
const resultsDiv = document.getElementById('autocomplete-results');

input.addEventListener('keydown', delayAutocomplete(function (e) {
  const searchTerm = input.value.trim();

  if (searchTerm === '') {
    resultsDiv.innerHTML = '';
    return;
  }

  searchDestinationsAJAX(searchTerm);
}, 500));

function searchDestinationsAJAX(searchTerm) {
  fetch(`/tpv/autocomplete/${searchTerm}`)
    .then(response => response.json())
    .then(data => {
      mostrarResultados(data);
    })
    .catch(error => {
      console.error('Error en la solicitud AJAX:', error);
    });
}

function mostrarResultados(results) {
  
  if (results.hasOwnProperty('error')) {
    resultsDiv.innerHTML = '<p>No se encontraron resultados</p>';
  } else {
    
    const listItems = results.map(result => `<div onclick="setItem('${result.name}','${result.geo.lat}','${result.geo.lat}')">${result.name} <span>${result.address}</span></div>`).join('');
    resultsDiv.innerHTML = listItems;

  }
}

//Delay on the autocomplete
function delayAutocomplete(callback, ms) {
    var timer = 0;
    return function () {
        var context = this,
            args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
            callback.apply(context, args);
        }, ms || 0);
    };
}

document.addEventListener('click', function (event) {
  const target = event.target;  
  if (target !== input && target !== resultsDiv) {
    resultsDiv.innerHTML = '';
  }
});