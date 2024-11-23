@extends('layout.blank')
@section('title') Test @endsection

@push('Css')
    <link rel="stylesheet" href="https://assets.ticker.co.uk/css/general.css">
    <style>
body,
html {
  height: 100vh;
  width: 100vw;
}

.grid-container {
  display: grid;
  grid-template-columns: 5em 1fr;
  grid-template-rows: 75px 1fr;
  grid-template-areas: "sidebar header" "sidebar main";
}

.sidebar {
  grid-area: sidebar;
  border-right: 1px solid grey;
}

.header {
  grid-area: header;
  border-bottom: 1px solid grey;
}

.main {
  grid-area: main;
}

.inner--grid {
  height: calc(100vh - 75px);
  display: grid;
  grid-template-columns: 5em 1fr;
  grid-template-rows: 1fr;
  grid-template-areas: "inner-sidebar inner-main";
}

.inner--grid > div {
  grid-area: inner-sidebar;
  border-right: 1px solid grey;
}

.inner--grid > main {
  grid-area: inner-main;
  background-color: #f1f1f1;
  overflow: auto;
}

.outer {
  display: inline-block;
  padding: 1.5em 2em;
  min-width: 100%;
  height: 100%;
}

.content {
  background-color: #ffffff;
  padding: 1em;
  height: 100%;
}

.content--item {
  background-color: #fff;
  border: 1px solid green;
  margin: 0 1em;
}

.content--item:nth-child(2n) {
  background-color: #e57474;
}

table {
  display: grid;
  width: 100%;
  height: 100%;
  grid-template-areas: "head-fixed" "body-scrollable";
  grid-template-rows: min-content;
}

table > thead {
  grid-area: head-fixed;
}

table > tbody {
  grid-area: body-scrollable;
  overflow: auto;
}

table tr {
  display: grid;
  grid-auto-flow: column;
}

    </style>
@endpush

@push('Js')
<script>
const tbody = document.querySelector('.content > table > tbody');
const array = new Array(100).fill(undefined);
console.log(array);

var fragment = document.createDocumentFragment();

for (var i = 0; i < array.length; i++) {
  var tr = document.createElement('tr');
  var td1 = document.createElement('td');
  var td2 = document.createElement('td');
  var td3 = document.createElement('td');
  var td4 = document.createElement('td');
  var td5 = document.createElement('td');
  var td6 = document.createElement('td');  
  var td7 = document.createElement('td');
  var td8 = document.createElement('td');
  var td9 = document.createElement('td');
  var td10 = document.createElement('td');

  var td11 = document.createElement('td');
  var td12 = document.createElement('td');
  var td13 = document.createElement('td');
  var td14 = document.createElement('td');
  var td15 = document.createElement('td');
  var td16 = document.createElement('td');  
  var td17 = document.createElement('td');
  var td18 = document.createElement('td');
  var td19 = document.createElement('td');
  var td20 = document.createElement('td');

  var td21 = document.createElement('td');
  var td22 = document.createElement('td');
  var td23 = document.createElement('td');
  var td24 = document.createElement('td');
  var td25 = document.createElement('td');
  var td26 = document.createElement('td');
  var td27 = document.createElement('td');
  var td28 = document.createElement('td');
  var td29 = document.createElement('td');
  var td30 = document.createElement('td');
  
  var td31 = document.createElement('td');
  var td32 = document.createElement('td');
  var td33 = document.createElement('td');
  var td34 = document.createElement('td');
  var td35 = document.createElement('td');
  var td36 = document.createElement('td');  
  var td37 = document.createElement('td');
  var td38 = document.createElement('td');  

  td1.appendChild(document.createTextNode("Cell " + i));
  td1.setAttribute('data-heading', 'Header ' + i)

  td2.appendChild(document.createTextNode("Cell " + i));
  td2.setAttribute('data-heading', 'Header ' + i)

  td3.appendChild(document.createTextNode("Cell " + i));
  td3.setAttribute('data-heading', 'Header ' + i)

  td4.appendChild(document.createTextNode("Cell " + i));
  td4.setAttribute('data-heading', 'Header ' + i)

  td5.appendChild(document.createTextNode("Cell " + i));
  td5.setAttribute('data-heading', 'Header ' + i)

  td6.appendChild(document.createTextNode("Cell " + i));
  td6.setAttribute('data-heading', 'Header ' + i)

  td7.appendChild(document.createTextNode("Cell " + i));
  td7.setAttribute('data-heading', 'Header ' + i)

  td8.appendChild(document.createTextNode("Cell " + i));
  td8.setAttribute('data-heading', 'Header ' + i)

  td9.appendChild(document.createTextNode("Cell " + i));
  td9.setAttribute('data-heading', 'Header ' + i)

  td10.appendChild(document.createTextNode("Cell " + i));
  td10.setAttribute('data-heading', 'Header ' + i)

  td11.appendChild(document.createTextNode("Cell " + i));
  td11.setAttribute('data-heading', 'Header ' + i)

  td12.appendChild(document.createTextNode("Cell " + i));
  td12.setAttribute('data-heading', 'Header ' + i)

  td13.appendChild(document.createTextNode("Cell " + i));
  td13.setAttribute('data-heading', 'Header ' + i)

  td14.appendChild(document.createTextNode("Cell " + i));
  td14.setAttribute('data-heading', 'Header ' + i)

  td15.appendChild(document.createTextNode("Cell " + i));
  td15.setAttribute('data-heading', 'Header ' + i)

  td16.appendChild(document.createTextNode("Cell " + i));
  td16.setAttribute('data-heading', 'Header ' + i)

  td17.appendChild(document.createTextNode("Cell " + i));
  td17.setAttribute('data-heading', 'Header ' + i)

  td18.appendChild(document.createTextNode("Cell " + i));
  td18.setAttribute('data-heading', 'Header ' + i)

  td19.appendChild(document.createTextNode("Cell " + i));
  td19.setAttribute('data-heading', 'Header ' + i)

  td20.appendChild(document.createTextNode("Cell " + i));
  td20.setAttribute('data-heading', 'Header ' + i)

  td21.appendChild(document.createTextNode("Cell " + i));
  td21.setAttribute('data-heading', 'Header ' + i)

  td22.appendChild(document.createTextNode("Cell " + i));
  td22.setAttribute('data-heading', 'Header ' + i)

  td23.appendChild(document.createTextNode("Cell " + i));
  td23.setAttribute('data-heading', 'Header ' + i)

  td24.appendChild(document.createTextNode("Cell " + i));
  td24.setAttribute('data-heading', 'Header ' + i)

  td25.appendChild(document.createTextNode("Cell " + i));
  td25.setAttribute('data-heading', 'Header ' + i)

  td26.appendChild(document.createTextNode("Cell " + i));
  td26.setAttribute('data-heading', 'Header ' + i)

  td27.appendChild(document.createTextNode("Cell " + i));
  td27.setAttribute('data-heading', 'Header ' + i)

  td28.appendChild(document.createTextNode("Cell " + i));
  td28.setAttribute('data-heading', 'Header ' + i)

  td29.appendChild(document.createTextNode("Cell " + i));
  td29.setAttribute('data-heading', 'Header ' + i)

  td30.appendChild(document.createTextNode("Cell " + i));
  td30.setAttribute('data-heading', 'Header ' + i)

  td31.appendChild(document.createTextNode("Cell " + i));
  td31.setAttribute('data-heading', 'Header ' + i)

  td32.appendChild(document.createTextNode("Cell " + i));
  td32.setAttribute('data-heading', 'Header ' + i)
  
  td33.appendChild(document.createTextNode("Cell " + i));
  td33.setAttribute('data-heading', 'Header ' + i)

  td34.appendChild(document.createTextNode("Cell " + i));
  td34.setAttribute('data-heading', 'Header ' + i)

  td35.appendChild(document.createTextNode("Cell " + i));
  td35.setAttribute('data-heading', 'Header ' + i)

  td36.appendChild(document.createTextNode("Cell " + i));
  td36.setAttribute('data-heading', 'Header ' + i)

  td37.appendChild(document.createTextNode("Cell " + i));
  td37.setAttribute('data-heading', 'Header ' + i)

  td38.appendChild(document.createTextNode("Cell " + i));
  td38.setAttribute('data-heading', 'Header ' + i)

    tr.appendChild(td1);
    tr.appendChild(td2);
    tr.appendChild(td3);
    tr.appendChild(td4);
    tr.appendChild(td5);
    tr.appendChild(td6);
    tr.appendChild(td7);
    tr.appendChild(td8);
    tr.appendChild(td9);
    tr.appendChild(td10);

    tr.appendChild(td11);
    tr.appendChild(td12);
    tr.appendChild(td13);
    tr.appendChild(td14);
    tr.appendChild(td15);
    tr.appendChild(td16);
    tr.appendChild(td17);
    tr.appendChild(td18);
    tr.appendChild(td19);
    tr.appendChild(td20);

    tr.appendChild(td21);
    tr.appendChild(td22);
    tr.appendChild(td23);
    tr.appendChild(td24);
    tr.appendChild(td25);
    tr.appendChild(td26);
    tr.appendChild(td27);
    tr.appendChild(td28);
    tr.appendChild(td29);
    tr.appendChild(td30);
    
    tr.appendChild(td31);
    tr.appendChild(td32);
    tr.appendChild(td33);
    tr.appendChild(td34);
    tr.appendChild(td35);
    tr.appendChild(td36);
    tr.appendChild(td37);
    tr.appendChild(td38);    

    fragment.appendChild(tr);
}

tbody.appendChild(fragment);    
</script>
@endpush

@section('content')
<div id="app">
    <div class="grid-container">
      <div class="sidebar">
        sidebar
      </div>
      <div class="header">Header</div>
      <div class="main">
        <div class="inner--grid">
          <div>inner sidebar</div>
          <main>
            <div class="outer">
              <div class="content">
                <table className="c-table--respond">
                  <thead>
                    <tr>
                      <td>Header 1</td>
                      <td>Header 2</td>
                      <td>Header 3</td>
                      <td>Header 4</td>
                      <td>Header 5</td>
                      <td>Header 6</td>
                      <td>Header 7</td>
                      <td>Header 8</td>
                      <td>Header 9</td>
                      <td>Header 10</td>
                      <td>Header 11</td>
                      <td>Header 12</td>
                      <td>Header 13</td>
                      <td>Header 14</td>
                      <td>Header 15</td>
                      <td>Header 16</td>
                      <td>Header 17</td>
                      <td>Header 18</td>
                      <td>Header 19</td>
                      <td>Header 20</td>
                      <td>Header 21</td>
                      <td>Header 22</td>
                      <td>Header 23</td>
                      <td>Header 24</td>
                      <td>Header 25</td>
                      <td>Header 26</td>
                      <td>Header 27</td>
                      <td>Header 28</td>
                      <td>Header 29</td>
                      <td>Header 30</td>
                      <td>Header 31</td>
                      <td>Header 32</td>
                      <td>Header 33</td>
                      <td>Header 34</td>
                      <td>Header 35</td>
                      <td>Header 36</td>
                      <td>Header 37</td>
                      <td>Header 38</td>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </main>
        </div>
      </div>
    </div>
  </div>
@endsection