(()=>{"use strict";const h="gg-tercih",r=(document.querySelector('meta[name="gg-olcum"]')||{}).content,c={...r?{analiz:{ad:"Analiz",sag:"Google Analytics",metin:"Hangi sayfaların ne kadar ziyaret edildiğini ölçer. Çerez kullanır; IP adresiniz, cihaz ve tarayıcı bilgileriniz Google’a iletilir."}}:{},harita:{ad:"Harita",sag:"Google Haritalar",metin:"Konumu sayfada gösterir. IP adresiniz ve tarayıcı bilgileriniz Google’a iletilir."}},n=Object.keys(c),s=()=>{try{const t=JSON.parse(localStorage.getItem(h));return!t||t.s!==2||(Date.now()-new Date(t.t).getTime())/864e5>180?null:t}catch{return null}},z=t=>{try{localStorage.setItem(h,JSON.stringify({s:2,t:new Date().toISOString(),...t}))}catch{}},k={harita:t=>{if(t.querySelector("iframe"))return;const a=document.createElement("iframe");a.src=t.dataset.kaynak,a.title=t.dataset.baslik,a.loading="lazy",a.referrerPolicy="strict-origin-when-cross-origin",a.allowFullscreen=!0,t.appendChild(a)}},w=()=>{if(window.gtag)return;window.dataLayer=window.dataLayer||[],window.gtag=function(){window.dataLayer.push(arguments)},window.gtag("consent","default",{analytics_storage:"granted",ad_storage:"denied",ad_user_data:"denied",ad_personalization:"denied"}),window.gtag("js",new Date),window.gtag("set","allow_google_signals",!1),window.gtag("set","allow_ad_personalization_signals",!1),window.gtag("config",r);const t=document.createElement("script");t.async=!0,t.src=`https://www.googletagmanager.com/gtag/js?id=${r}`,document.head.appendChild(t)},v=()=>{const t=["",`; domain=${location.hostname}`,`; domain=.${location.hostname}`];document.cookie.split("; ").map(a=>a.split("=")[0]).filter(a=>/^_ga(_|$)/.test(a)).forEach(a=>t.forEach(e=>{document.cookie=`${a}=; Max-Age=0; path=/${e}`}))},f=t=>{r&&(t&&t.analiz?w():v()),document.querySelectorAll("[data-rizali]").forEach(a=>{const e=a.dataset.rizali;!t||!t[e]||(a.classList.add("is-izinli"),k[e](a))})},o=t=>{const a=s()||{};if(z(t),A(),n.some(e=>a[e]&&!t[e])){location.reload();return}f(t)},d=t=>n.reduce((a,e)=>(a[e]=t,a),{}),S=document.querySelector(".trc"),l=document.documentElement,A=()=>{l.classList.add("trc-var"),l.classList.remove("trc-acik")},_=()=>{l.classList.remove("trc-var"),l.classList.add("trc-acik")},i=document.createElement("dialog");i.className="trc-pencere",i.setAttribute("aria-labelledby","trc-baslik"),i.setAttribute("data-lenis-prevent",""),i.innerHTML=`
    <form method="dialog">
      <h2 id="trc-baslik">Çerez tercihleri</h2>
      <p>Seçiminizi istediğiniz zaman alt bilgideki “Çerez Tercihleri” bağlantısından değiştirebilirsiniz.</p>
      <div class="trc-satir">
        <div><h3>Zorunlu</h3><p>Tercihinizi hatırlamak için bu tarayıcıda 180 gün saklanan bir kayıt. Kimliğinizi belirtmez, sunucumuza gönderilmez.</p></div>
        <span class="trc-sabit">Her zaman açık</span>
      </div>
      <div class="trc-satir">
        <div><h3>Online randevu takvimi</h3><p>İletişim sayfasında izin beklemeden yüklenir. Doktor Takvimi çerez ve yerel depolama kullanır, analiz ve hata kaydı hizmetlerine bağlanır. Sağlayıcı: Doktor Takvimi.</p></div>
        <span class="trc-sabit">Her zaman açık</span>
      </div>
      ${n.map(t=>`
      <label class="trc-satir" for="trc-${t}">
        <div><h3>${c[t].ad}</h3><p>${c[t].metin} Sağlayıcı: ${c[t].sag}.</p></div>
        <input type="checkbox" id="trc-${t}" name="${t}">
      </label>`).join("")}
      <div class="trc__dugmeler">
        <button type="button" data-trc="kabul">Kabul et</button>
        <button type="button" data-trc="ret">Reddet</button>
        <button type="button" data-trc="kaydet">Seçimi kaydet</button>
      </div>
    </form>`;const y=()=>{const t=s()||{};n.forEach(a=>{i.querySelector(`#trc-${a}`).checked=!!t[a]}),i.open||i.showModal()},u=()=>{i.open&&i.close()},p=t=>{const a=t.target.closest("[data-trc]");if(!a)return;const e=a.dataset.trc;if(e==="kabul")u(),o(d(!0));else if(e==="ret")u(),o(d(!1));else if(e==="tercih")y();else if(e==="kaydet"){const g={};n.forEach(m=>{g[m]=i.querySelector(`#trc-${m}`).checked}),u(),o(g)}};S.addEventListener("click",p),i.addEventListener("click",p),document.body.append(i),document.addEventListener("click",t=>{if(t.target.closest("[data-tercih-ac]")){t.preventDefault(),y();return}const e=t.target.closest("[data-izin-ver]");if(e){const{s:g,t:m,...L}=s()||{};o({...d(!1),...L,[e.dataset.izinVer]:!0})}});const b=s();f(b),b||_()})();
