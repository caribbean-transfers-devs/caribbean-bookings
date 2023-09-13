// The autoComplete.js Engine instance creator
const autoCompleteJS = new autoComplete({
    selector: "#bookingFromForm",
    placeHolder: "Ingresa el origen...",
    data: {
        src: async (query) => {
          try {
            // Fetch Data from external Source
            const source = await fetch(`/tpv/autocomplete/${query}`);
            // Data should be an array of `Objects` or `Strings`
            const data = await source.json();
    
            return data;
          } catch (error) {
            return error;
          }
        },
        // Data source 'Object' key to be searched
        keys: ["food"]
    },
    resultItem: {
        highlight: true,
    }
});
  
/*
  // Toggle Search Engine Type/Mode
  document.querySelector(".toggler").addEventListener("click", () => {
    // Holds the toggle button selection/alignment
    const toggle = document.querySelector(".toggle").style.justifyContent;
  
    if (toggle === "flex-start" || toggle === "") {
      // Set Search Engine mode to Loose
      document.querySelector(".toggle").style.justifyContent = "flex-end";
      document.querySelector(".toggler").innerHTML = "Loose";
      autoCompleteJS.searchEngine = "loose";
    } else {
      // Set Search Engine mode to Strict
      document.querySelector(".toggle").style.justifyContent = "flex-start";
      document.querySelector(".toggler").innerHTML = "Strict";
      autoCompleteJS.searchEngine = "strict";
    }
  });
  
  // Blur/unBlur page elements
  const action = (action) => {
    const github = document.querySelector(".github-corner");
    const title = document.querySelector("h1");
    const mode = document.querySelector(".mode");
    const selection = document.querySelector(".selection");
    const footer = document.querySelector(".footer");
  
    if (action === "dim") {
      github.style.opacity = 1;
      title.style.opacity = 1;
      mode.style.opacity = 1;
      selection.style.opacity = 1;
      footer.style.opacity = 1;
    } else {
      github.style.opacity = 0.1;
      title.style.opacity = 0.3;
      mode.style.opacity = 0.2;
      selection.style.opacity = 0.1;
      footer.style.opacity = 0.1;
    }
  };
  
  // Blur/unBlur page elements on input focus
  ["focus", "blur"].forEach((eventType) => {
    autoCompleteJS.input.addEventListener(eventType, () => {
      // Blur page elements
      if (eventType === "blur") {
        action("dim");
      } else if (eventType === "focus") {
        // unBlur page elements
        action("light");
      }
    });
  });
*/