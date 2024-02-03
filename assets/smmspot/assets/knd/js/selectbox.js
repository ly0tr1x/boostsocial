"use strict";

/** 
 * Selectbox.js
 * created by suleymankndlc
 * version: 0.1
*/

const sspSbGen = (select) => {
    let selectInp = select;
    let classes = select.getAttribute('data-class');
    
    var el = document.createElement('div');
    el.classList.add('smmspot-sb-container');
    select.after(el);

    selectInp.style.display = "none";

    var selectBtn = document.createElement('button');
    selectBtn.classList.add('smmspot-sb-btn');
    selectBtn.type = "button";
    el.appendChild(selectBtn);

    if (classes) {
        selectBtn.className = selectBtn.className +' '+ classes;
    }

    var selectBtnSelected = document.createElement('span');
    selectBtnSelected.classList.add('smmspot-sb-selected');
    selectBtnSelected.innerText = "Please select"
    selectBtn.appendChild(selectBtnSelected);

    var selectDD = document.createElement('div');
    selectDD.classList.add('smmspot-sb-dropdown');
    el.appendChild(selectDD);


    var selectDDBody = document.createElement('div');
    selectDDBody.classList.add('smmspot-sb-dd-body');
    selectDD.appendChild(selectDDBody);

    var selectMobileDrag = document.createElement('div');
    selectMobileDrag.classList.add('smmspot-sb-mobiledrag');
    selectDDBody.appendChild(selectMobileDrag);

    selectDDBody.insertAdjacentHTML('beforeend', '<div class="smmspot-sb-dd-search-c"><div class="smmspot-sb-dd-search"><input type="text" class="smmspot-sb-dd-input" placeholder="search "></div></div>')

    var selectDDContent = document.createElement('div');
    selectDDContent.classList.add('smmspot-sb-dd-content');
    selectDDBody.appendChild(selectDDContent);

    var selectMobileBg = document.createElement('div');
    selectMobileBg.classList.add('smmspot-sb-mobilebg');
    el.appendChild(selectMobileBg);


    var clientY;

    selectMobileDrag.addEventListener('touchstart', e => {
        clientY = e.touches[0].clientY;
        selectMobileDrag.classList.add('active');
    }, false);

    var ftop = 0;
    selectMobileDrag.addEventListener('touchmove', e => {
        ftop = e.touches[0].clientY - clientY;
        if( !ftop <= clientY ) {
            selectDD.style.transform = 'translateY('+ ftop +'px)';
        }
    });

    selectMobileDrag.addEventListener('touchend', e => {
        var deltaY = e.changedTouches[0].clientY;
        selectMobileDrag.classList.remove('active');

        if ( deltaY - 80 > clientY ) {
            closeSspSbGen(el);
            selectDD.style.transform = '';
        } else {
            selectDD.style.transform = '';
        }
    });

    el.getElementsByClassName('smmspot-sb-btn')[0].addEventListener('click', () => {
        if (el.classList.contains('active')) {
            closeSspSbGen(el);
        } else {
            selectDD.style.transition = '.2s ease';
            el.classList.add('active');
            document.body.classList.add('sspSbOpen');
            setTimeout(() => {
                selectDD.style.transition = '';
            }, 220);
        }
    });
    
    const genOpts = (options) => {
        selectDDContent.innerHTML = "";
        for (let index = 0; index < options.length; index++) {

            let option = document.createElement('button');
            option.classList.add('smmspot-sb-dd-item');
            option.innerHTML = options[index].text;
            option.type = 'button';

            if(options[index].disabled) {
                option.classList.add('disabled');
                option.setAttribute('data-disabled', 'disabled');
            }

            if(options[options.selectedIndex] == options[index]) {
                option.classList.add('selected');
                selectBtnSelected.innerText = options[index].text;
            }
 
            option.setAttribute('data-value', options[index].value);
            selectDDContent.appendChild(option);
        }
    
        let opts = el.getElementsByClassName('smmspot-sb-dd-item');
    
        if (opts) {
            [...opts].forEach(opt => {
                opt.addEventListener('click', () => {
                    let val = opt.getAttribute('data-value');
                    let disabled = opt.getAttribute('data-disabled');

                    if( !disabled ) {
                        el.getElementsByClassName('selected')[0].classList.remove('selected');
                        selectInp.value = val;
                        let selectedItem = selectInp.querySelector('[selected]');
                        if(selectedItem) {
                            selectedItem.removeAttribute('selected');
                        }
                        selectInp.querySelector('[value="'+ val +'"]').setAttribute('selected', '');
                        selectBtnSelected.innerText = opt.innerText;
                        opt.classList.add('selected');

                        var event = document.createEvent('HTMLEvents');
                        event.initEvent('change', true, false);
                        selectInp.dispatchEvent(event);

                        closeSspSbGen(el);
                    }
                })
            });
        }
    }

    let options = selectInp.options;
    genOpts(options);

    selectInp.addEventListener('DOMSubtreeModified', () => {
        let options = selectInp.options;
        genOpts(options);
    });

    const  searchIn = (value) => {
        var nodes = el.getElementsByClassName('smmspot-sb-dd-item');

        for (let i = 0; i < nodes.length; i++) {
            if (nodes[i].innerText.toLowerCase().includes(value.toLowerCase())) {
                nodes[i].style.display = "block";
            } else {
                if(!nodes[i].classList.contains('selected')) nodes[i].style.display = "none";;
            }
        }
    }

    let searchInput = el.getElementsByClassName('smmspot-sb-dd-input');
    let shasclose = false;
    if(searchInput) {
        searchInput[0].addEventListener('keyup', () => {
            let value = searchInput[0].value;
            if (value === '' && shasclose == true) {
                //** boş */
                shasclose = false;
            }

            if (value !== '' && shasclose == false) {
                //** dolu */
                shasclose = true;
            }
            searchIn(value);
        });
    }

    const resizeDD = () => {
        if (window.innerWidth < 768) {
            selectDD.style.height = `${window.innerHeight - 100}px`;
        } else {
            selectDD.style.height = 'inherit';
        }
    }
    
    resizeDD();

    addEventListener('resize', resizeDD);

    const closeSspSbGen = (el) => {
        selectDD.style.transition = '.2s ease';
        el.classList.remove('active');
        setTimeout(() => {
            selectDD.style.transition = '';
        }, 220);
        document.body.classList.remove('sspSbOpen');
    }

    window.addEventListener('click', function(e){   
        if (!el.contains(e.target) && el.classList.contains('active')){
            closeSspSbGen(el);
        }
    });

    selectMobileBg.addEventListener('click', () => {
        closeSspSbGen(el);
    });
}

var selectInp = document.querySelectorAll('[data-toggle="ssp-selectbox"]');

if (selectInp) {
    [...selectInp].forEach(select => {
        sspSbGen(select);
    });
}