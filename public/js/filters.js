window.onload = () => {
    const filtersForm = document.querySelector('#filters');

    document.querySelectorAll("#filters input").forEach(input => {
        input.addEventListener("change", () => {

            const Form = new FormData(filtersForm);
            const Params = new URLSearchParams();

            Form.forEach((value, key) => {
                Params.append(key, value)
            });

            const Url = new URL(window.location.href);

            fetch(Url.pathname + "?" + Params.toString() + "&ajax=1", {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            }).then(response =>
                response.json()
            ).then(
                data => {
                    const content = document.querySelector('#content');
                    const noComponent = document.querySelector('#nocomponent');

                    if(data.content == ""){
                        noComponent.classList.remove("hidden")
                        content.classList.add("hidden")
                        content.innerHTML = data.content;
                    }else{
                        noComponent.classList.add("hidden")
                        content.classList.remove("hidden")
                        content.innerHTML = data.content;
                    }

                }
            ).catch(e => alert(e))

        })
    })
}