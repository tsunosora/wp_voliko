/* cloodo core js version 1 */
const delay = ms => new Promise(res => setTimeout(res, ms));
let cloodo_link = document.getElementById('cloodo_link');
window.addEventListener('load', async function (e) {
  if (cloodo_link){
    cloodo_link.addEventListener('click',clickCloodoLink);
    
    if (localStorage.autoLoadIframe){
      cloodo_link.textContent = ' iframe automatic loading... Click here to disable it.';
      await delay(3000);
      loadCloodoPage(e);
    }else{
      cloodo_link.textContent += ' iframe will automatic load on next time';
    }
  }
});

async function clickCloodoLink(e){
  e.preventDefault();
  if (localStorage.autoLoadIframe){
    cloodo_link.textContent = ' iframe automatic disabled.';
    localStorage.removeItem('autoLoadIframe');
  }else{
    localStorage.autoLoadIframe = true;
    cloodo_link.textContent = ' iframe automatic loading...';
    loadCloodoPage(e);
  }
}

function loadCloodoPage(e){
  if (!localStorage.autoLoadIframe){
    return;
  }
  let cloodo_page = document.createElement('iframe');
  let cloodo_admin_default = document.getElementById('cloodo_admin_default');
  
  cloodo_page.setAttribute('id','cloodo_link');
  cloodo_page.setAttribute('src',cloodo_link.getAttribute('href'));
  cloodo_admin_default.replaceWith(cloodo_page);
}