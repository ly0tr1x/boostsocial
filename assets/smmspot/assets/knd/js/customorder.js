"use strict"
/**
 * customOrder.js
 * created by suleymankndlc
 * version: 0.1
 */

const sspCustomOrder = (x) => {

    /** variables  */
    if ( x.servicesContainer ) {
        var servicesContainer = document.getElementById(x.servicesContainer);
        if (!servicesContainer) console.log('serviceContainer Error');
    }

    if ( x.services ) {
        var services = x.services;
    }

    var categoryInp = document.getElementById('orderform-category');
    var servicesInp = document.getElementById('orderform-service');
    var activeCat = 0;
    var activeServices = [];

    const category = () => {
        activeCat = categoryInp.value;
    }
    category();

    /** prepare service block */
    var serviceBlockElement = servicesContainer.getElementsByClassName('ssp-service-item')[0];
    if(!serviceBlockElement) {
        console.log('Service block element not found!');
    }

    var serviceBlock = serviceBlockElement.outerHTML;
    serviceBlockElement.remove();


    var omActive = false;
    var om = document.getElementById("orderModal");
    var omBox = document.getElementById("orderModalBox");
    var omBg = document.getElementById('orderModalBg');
    var omHeader = document.getElementById('orderModalHeader');
    var omToggleBtn = document.getElementById('orderModalToggle');
    var omDescText = document.getElementById('sspModalDescLoad');
    var omItemContent = null;
    //** orderModal */

    const orderModal = (item) => {
        if (item) {
            omDescText.innerHTML = item['description'];
        }
        om.classList.add('active');
        om.classList.add('pre-active');
        omBg.style.display = 'block';
        document.body.classList.add('sspModalOpen');
        omActive = true;
    }

    const omClose = () => {
        om.classList.remove('active');
        omBg.style.display = 'none';
        omActive = false;
        document.body.classList.remove('sspModalOpen');
    }

    omBg.addEventListener('click', omClose);

    omHeader.addEventListener('click', () => {
        if (omActive) {omClose();} else {orderModal();}
    });

    /** generate service block  */
    const genServiceBlock = (id = 1) => {
        var item = serviceBlock;

        let matches = item.match(/\[\[(.*?)]]/gm);

        if(matches) {
            matches.map((match) => {
                let itemKey = match.replace(/[\[\]']+/g, '');

                let replaceWith = 'undefined';

                replaceWith = services[id][itemKey];
                item = item.replace(match, replaceWith);
            });
            
            servicesContainer.insertAdjacentHTML('beforeend', item);
        }

        let serviceItem = servicesContainer.querySelector(`[data-service-id="${id}"]`);
        let radioBtn = serviceItem.querySelector('[type="radio"]');
        let selectBtn = serviceItem.querySelector('[data-service-select]');
        radioBtn.addEventListener('change', e => {
            if(servicesInp) {
                servicesInp.value = id;
            } else {
                console.log('Services Input not found');
            }
            orderModal(services[id]);

        });
        selectBtn.addEventListener('click', e => {
            radioBtn.click();
        });
    }

    /** generate services  */
    const generateServices = (cid = 1) => {
        servicesContainer.innerHTML = '';
        activeServices = [];
        let iCsf = 0;
        for (const [key, value] of Object.entries(services)) {
            if(cid == value.cid) {
                genServiceBlock(key);
                activeServices[iCsf] = {service: key};
                iCsf++;
            }
        }
    }

    //** sorting */
    var sortItems = document.querySelectorAll('[data-service-sort]');
    if ( sortItems ) {
        [...sortItems].forEach(sortItem => {
            var sortWhat = sortItem.getAttribute('data-service-sort');
            var sortType = sortItem.getAttribute('data-sort-type');
            sortItem.addEventListener('click', () => {
                var sortServices = [];
                if (activeServices) {

                    for (let [key, value] of Object.entries(activeServices)) {
                        sortServices[key] =  {id: value.service, sort: services[value.service][sortWhat]};
                    }   


                    sortServices.sort(function (a, b) {
                        if (sortType === 'desc') {
                            return b.sort - a.sort;
                        } else if (sortType === 'asc') {
                            return a.sort - b.sort;
                        } else {
                            sortServices = activeServices;
                        }
                    });

                    [...sortItems].forEach(element => {
                        element.classList.remove('active');
                    });
                    sortItem.classList.add('active');
                    servicesContainer.innerHTML = '';

                    for (const [key, value] of Object.entries(sortServices)) {
                        genServiceBlock(value.id);
                    }
                }
            });
        });
    }

    /** start */
    generateServices(activeCat);

    categoryInp.addEventListener('change', e => {
        category();
        generateServices(activeCat);
    })
}