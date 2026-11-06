(()=>{"use strict";const h="gg-tercih",r=(document.querySelector('meta[name="gg-olcum"]')||{}).content,c={...r?{analiz:{ad:"Analiz",sag:"Google Analytics",metin:"Hangi sayfaların ne kadar ziyaret edildiğini ölçer. Çerez kullanır; IP adresiniz, cihaz ve tarayıcı bilgileriniz Google’a iletilir."}}:{},harita:{ad:"Harita",sag:"Google Haritalar",metin:"Konumu sayfada gösterir. IP adresiniz ve tarayıcı bilgileriniz Google’a iletilir."}},n=Object.keys(c),s=()=>{try{const t=JSON.parse(localStorage.getItem(h));return!t||t.s!==2||(Date.now()-new Date(t.t).getTime())/864e5>180?null:t}catch{return null}},z=t=>{try{localStorage.setItem(h,JSON.stringify({s:2,t:new Date().toISOString(),...t}))}catch{}},k={harita:t=>{if(t.querySelector("iframe"))return;const e=document.createElement("iframe");e.src=t.dataset.kaynak,e.title=t.dataset.baslik,e.loading="lazy",e.referrerPolicy="strict-origin-when-cross-origin",e.allowFullscreen=!0,t.appendChild(e)}},v=()=>{if(window.gtag)return;window.dataLayer=window.dataLayer||[],window.gtag=function(){window.dataLayer.push(arguments)},window.gtag("js",new Date),window.gtag("config",r,{allow_google_signals:!1,allow_ad_personalization_signals:!1});const t=document.createElement("script");t.async=!0,t.src=`https://www.googletagmanager.com/gtag/js?id=${r}`,document.head.appendChild(t)},w=()=>{const t=["",`; domain=${location.hostname}`,`; domain=.${location.hostname}`];document.cookie.split("; ").map(e=>e.split("=")[0]).filter(e=>/^_ga(_|$)/.test(e)).forEach(e=>t.forEach(a=>{document.cookie=`${e}=; Max-Age=0; path=/${a}`}))},f=t=>{r&&(t&&t.analiz?v():w()),document.querySelectorAll("[data-rizali]").forEach(e=>{const a=e.dataset.rizali;!t||!t[a]||(e.classList.add("is-izinli"),k[a](e))})},l=t=>{const e=s()||{};if(z(t),A(),n.some(a=>e[a]&&!t[a])){location.reload();return}f(t)},d=t=>n.reduce((e,a)=>(e[a]=t,e),{}),S=document.querySelector(".trc"),o=document.documentElement,A=()=>{o.classList.add("trc-var"),o.classList.remove("trc-acik")},L=()=>{o.classList.remove("trc-var"),o.classList.add("trc-acik")},i=document.createElement("dialog");i.className="trc-pencere",i.setAttribute("aria-labelledby","trc-baslik"),i.setAttribute("data-lenis-prevent",""),i.innerHTML=`
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
    </form>`;const y=()=>{const t=s()||{};n.forEach(e=>{i.querySelector(`#trc-${e}`).checked=!!t[e]}),i.open||i.showModal()},u=()=>{i.open&&i.close()},b=t=>{const e=t.target.closest("[data-trc]");if(!e)return;const a=e.dataset.trc;if(a==="kabul")u(),l(d(!0));else if(a==="ret")u(),l(d(!1));else if(a==="tercih")y();else if(a==="kaydet"){const m={};n.forEach(g=>{m[g]=i.querySelector(`#trc-${g}`).checked}),u(),l(m)}};S.addEventListener("click",b),i.addEventListener("click",b),document.body.append(i),document.addEventListener("click",t=>{if(t.target.closest("[data-tercih-ac]")){t.preventDefault(),y();return}const a=t.target.closest("[data-izin-ver]");if(a){const{s:m,t:g,...$}=s()||{};l({...d(!1),...$,[a.dataset.izinVer]:!0})}});const p=s();f(p),p||L()})();
