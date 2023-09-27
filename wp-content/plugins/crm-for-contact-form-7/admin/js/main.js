var iframeCloodo = document.getElementById("cloodo_frame");
window.addEventListener("message", function(e){
  if (e.data == 'cloodo iframe loaded'){
    console.log('parent get message', e.data);
    
    iframeCloodo.contentWindow.postMessage(ccf7_obj.cloodo_token, '*');
    console.log('posted message');
  }
  if (e.data == 'cloodo login successfully'){
    location.reload();
  }
  if (e.data == 'cloodo login required'){
    iframeCloodo.src = ccf7_obj.iframe_url + '/check-login';
  }
});
