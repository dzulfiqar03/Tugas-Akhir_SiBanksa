import{u as ye,g as n,a as M,b as i,h as ve,w as z,d as t,o as s,f as C,i as S,n as _,t as y,T as we,c as O,_ as le,j as U,v as ie,F as v,q as T,z as J,e as De,B as W,r as I,y as Ce,C as G}from"./app-Zqy0v9cC.js";import{_ as $e}from"./AuthenticatedLayout-CgWEcJ3z.js";import{S as g}from"./sweetalert2.esm.all-BjNi2kua.js";import{_ as q}from"./InputLabel-DUHDXCW2.js";import"./responsive.dataTables-5T0S2NYL.js";import{j as Se,v as Y,b as _e}from"./vfs_fonts-C-97PlGI.js";import{V as A,D as K}from"./datatables.net-vue3-B_9dYv7y.js";/* empty css            */import"./_plugin-vue_export-helper-DlAUqK2U.js";import"./Preloader-DAd158ok.js";import"./404-BW-iLsrq.js";import"./SessionExpired-a_r78cOD.js";/* empty css                                                               */import"./_commonjs-dynamic-modules-TDtrdbi3.js";const Te={class:"space-y-6"},Be={class:"flex flex-col md:flex-row md:items-center justify-between gap-4"},je={class:"flex space-x-3"},Ee={key:0},Re={key:1,"x-cloak":"",class:"flex items-center gap-3 px-4 py-3 rounded-xl border border-red-200 bg-red-50 dark:bg-red-900/10 dark:border-red-500/20 shadow-sm transition-all"},ze={class:"text-xs font-medium text-red-800 dark:text-red-300"},Pe={key:0},Ie={key:1},Ae={key:2},Le={class:"flex p-1.5 mb-5 bg-gray-100 dark:bg-gray-800/50 rounded-2xl"},Me={key:0,class:"bg-white accordion-wrapper overflow-hidden dark:bg-gray-800 p-6 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700"},Fe={class:"text-lg w-full font-semibold mb-4 text-black dark:text-white"},Ne={class:"flex flex-col w-full"},Ue={class:"flex flex-col"},Ke=["value"],Oe={key:0,class:"flex flex-col"},He=["value"],Ve={key:0,class:"flex flex-col"},Je=["type","id","placeholder"],We={key:0,class:"text-xs text-emerald-600 mt-2 font-medium"},Ge={key:1,class:"mt-2 space-y-1"},qe={class:"flex items-center"},Ye=["onClick"],Ze={class:"md:col-span-2 lg:col-span-3 flex justify-end items-center gap-3 pt-2"},Qe=["disabled"],Xe={class:"grid grid-cols-1 md:grid-cols-2 gap-4"},et={class:"flex flex-col"},tt=["value"],at={key:0,class:"flex flex-col"},rt=["type","id","placeholder"],ot={key:0,class:"text-xs text-emerald-600 mt-2 font-medium"},st={key:1,class:"mt-2 space-y-1"},nt={class:"flex items-center"},lt=["onClick"],it={class:"md:col-span-2 lg:col-span-3 flex justify-end items-center gap-3 pt-2"},dt=["disabled"],ut={class:"bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700"},ct={key:0,class:"flex flex-col lg:flex-row lg:items-end justify-between mb-6"},pt={class:"flex flex-wrap mb-5 lg:mb-0 items-center gap-2"},mt={class:"flex flex-wrap md:flex-nowrap items-end justify-start gap-3"},gt={class:"flex items-end gap-2"},xt={class:"flex items-center gap-2"},ft={class:"flex items-center gap-2 pl-3"},kt={key:1,class:"flex flex-col lg:flex-row lg:items-end justify-between mb-6"},ht={class:"flex flex-wrap mb-5 lg:mb-0 items-center gap-2"},bt={class:"flex flex-wrap md:flex-nowrap items-end justify-start gap-3"},yt={class:"flex items-end gap-2"},vt={class:"flex items-center gap-2"},wt=["value"],Dt={class:"flex items-center gap-2 pl-3"},Ct={class:"bg-white dark:bg-gray-800 rounded-xl"},$t={class:"relative max-w-4xl w-full flex flex-col items-center"},St={key:0,class:"w-full h-[80vh] md:h-[85vh]"},_t=["src"],Tt={key:1,class:"w-full h-full flex items-center justify-center p-4"},Bt=["src"],Ht={__name:"PelaporanKetuaRW",props:{formdata:Object,sidebardata:Object,IDUser:Number,document:Array,image:Array,jadwalPelaksanaan:Array,IDRT:Number,IDRW:Number},setup(B){A.use(K),A.use(K),A.use(K),A.use(K),A.use(K),window.JSZip=Se;const de=Y.pdfMake?Y.pdfMake.vfs:Y.vfs;_e.vfs=de;const d=I("Document"),p=B,j=I(!1),ue=I(1),P=I(!1),E=I(null),R=Ce(),H=I(!1),V=I(""),ce=r=>{const e=encodeURIComponent(r.trim());V.value=d.value==="Document"?`/storage/files/documentUser/BankSampah/RT0${p.IDRT}/${e}`:`/storage/photo/evidenceUser/BankSampah/RT0${p.IDRT}/${e}`,H.value=!0},Z=()=>{H.value=!1};window.handleOpenPreview=ce;const pe=r=>{g.fire({title:"Hapus data?",text:"Tindakan ini tidak bisa dibatalkan!",icon:"warning",showCancelButton:!0,confirmButtonColor:"#ef4444",confirmButtonText:"Ya, Hapus!"}).then(e=>{e.isConfirmed&&(d.value==="Document"?W.delete(route("delete-document",r),{onSuccess:()=>g.fire("Dihapus!","Dokumen berhasil dihapus.","success")}):W.delete(route("delete-evidence",r),{onSuccess:()=>g.fire("Dihapus!","Evidence berhasil dihapus.","success")}))})};window.deleteDoc=pe;const me=r=>{let e=d.value==="Document"?r.document.map(a=>`
        <div class="flex flex-col items-center gap-2 p-2 bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700 shadow-sm">
            <div onclick="window.handleOpenPreview('${a.original_filesname}')" class="w-12 h-12 flex flex-col items-center justify-center bg-red-50 text-red-500 rounded-lg border border-red-100">
                <i class="fas fa-file-pdf text-xl"></i>
                <span class="text-[8px] font-bold mt-1">PDF</span>
               </div>
            <span class="text-[10px] text-gray-500 truncate w-20 text-center">${a.original_filesname}</span>
             <div class="flex justify-center gap-1">

                            <button onclick="window.deleteDoc(${a.id})" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
        </div>
    `).join(""):r.photos.map(a=>`
        <div class="flex flex-col items-center gap-2 p-2 bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700 shadow-sm">
            <img
            src="/storage/photo/evidenceUser/BankSampah/RT0${p.IDRT}/${a.original_photoname}"
            alt="Dokumen" onclick="window.handleOpenPreview('${a.original_photoname}')"
            class="w-12 h-12 rounded-lg object-cover border border-gray-200 shadow-sm hover:scale-110 transition-transform cursor-pointer"
        />
            <span class="text-[10px] text-gray-500 truncate w-20 text-center">${a.original_photoname}</span>
                <div class="flex justify-center gap-1">

                            <button onclick="window.deleteDoc(${a.id})" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
        </div>
    `).join("");return d.value==="Document"?`
        <div class="p-4 bg-gray-50 dark:bg-gray-900 border-l-4 border-emerald-500">
            <p class="text-xs font-bold text-emerald-600 mb-3 uppercase tracking-wider">Dokumen (${r.document.length} File):</p>
            <div class="flex flex-wrap gap-4">
                ${e||'<p class="text-gray-400 italic">Tidak ada Dokumen.</p>'}
            </div>
        </div>
    `:`
        <div class="p-4 bg-gray-50 dark:bg-gray-900 border-l-4 border-emerald-500">
            <p class="text-xs font-bold text-emerald-600 mb-3 uppercase tracking-wider">Dokumentasi Foto (${r.photos.length} File):</p>
            <div class="flex flex-wrap gap-4">
                ${e||'<p class="text-gray-400 italic">Tidak ada foto.</p>'}
            </div>
        </div>
    `},Q=r=>{const e=r.target.closest("tr"),a=e.querySelector(".fa-chevron-right"),l=E.value.dt.row(e);l.child.isShown()?(l.child.hide(),e.classList.remove("shown"),a&&(a.style.transform="rotate(0deg)")):(l.child(me(l.data())).show(),e.classList.add("shown"),a&&(a.style.transform="rotate(90deg)"))},ge=r=>{o.fileDoc.splice(r,1)},xe=r=>{o.imgEvidence.splice(r,1)},X=G(()=>({pageLength:5,responsive:!0,lengthMenu:[5,10,25,50],columns:d.value==="Document"?[{data:null,orderable:!1,className:"no-print details-control text-center"},{data:"name",render:r=>`<strong>Dokumen: ${r}</strong>`},{data:"tanggal_setoran",render:r=>`<strong>Jadwal: ${r}</strong>`},{data:"document",render:r=>`<span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-xs font-bold">${r.length} Dokumen</span>`}]:[{data:null,orderable:!1,className:"no-print text-black dark:text-white details-control text-center text-black dark:text-white"},{data:"name",render:r=>`<strong>Jadwal: ${r}</strong>`},{data:"photos",render:r=>`<span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-xs font-bold">${r.length} Foto</span>`}],layout:{topStart:null,topEnd:null,bottomStart:"info",bottomEnd:"paging"},buttons:[{extend:"pdfHtml5",text:'<i class="fa-solid fa-file-pdf mr-2"></i> PDF',pageSize:"A4",title:"Laporan "+d.value+" SiBanksa RT"+(R.props.auth.user.user_detail?.id_rt||"-")+" Tanggal "+new Date().toLocaleDateString("id-ID").replace(/\//g,"-"),className:"export-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm",action:async function(r,e,a,l){const u=this,m=d.value;g.fire({title:"Memproses PDF...",text:`Menyiapkan lampiran ${m.toLowerCase()}...`,allowOutsideClick:!1,didOpen:()=>g.showLoading()});const w=x=>new Promise(f=>{const c=new Image;c.setAttribute("crossOrigin","anonymous"),c.onload=()=>{const k=document.createElement("canvas");k.width=c.width,k.height=c.height,k.getContext("2d").drawImage(c,0,0),f(k.toDataURL("image/png"))},c.onerror=()=>f(null),c.src=x}),D=[];for(const x of F.value){const f=[],c=m==="Evidence",k=c?x.photos||[]:x.document||[],h=c?"photo/evidenceUser":"files/documentUser";for(const b of k){const N=c?b.original_photoname:b.original_filesname;if(!N)continue;const be=`${window.location.origin}/storage/${h}/BankSampah/RT0${p.IDRT}/${N}`;let ne=null;c&&(ne=await w(be)),f.push({b64:ne,name:N})}f.length>0&&D.push({tanggal:x.tanggal_setoran,files:f})}l.customize=function(x){g.close();const f=m==="Evidence",c=x.content.find(k=>k.table);c&&c.table.body.length>0&&(f?c.table.widths=[30,"*",100]:c.table.widths=[30,"*",100,80],c.table.body.forEach((k,h)=>{k.forEach(b=>{b&&(b.fontSize=9,h===0&&(b.fillColor="#10b981",b.color="white",b.bold=!0))})})),x.content.splice(0,1,{columns:[{stack:[{text:"SiBanksa",fontSize:18,bold:!0,color:"#10b981"},{text:"Laporan Digital Bank Sampah",fontSize:7,color:"#9ca3af"}]},{stack:[{text:`LAPORAN ${m.toUpperCase()}`,fontSize:12,bold:!0,alignment:"right"},{text:`UNIT RT-0${p.IDRT}`,fontSize:8,alignment:"right",color:"#6b7280"}],width:"*"}],margin:[0,0,0,15]}),D.length>0&&(x.content.push({text:`
LAMPIRAN ${f?"FOTO":"BERKAS"}:`,fontSize:10,bold:!0,color:f?"#065f46":"#b91c1c",margin:[0,10,0,5]}),D.forEach(k=>{if(x.content.push({table:{widths:["*"],body:[[{text:`Jadwal: ${k.tanggal}`,bold:!0,fontSize:8,color:f?"#065f46":"#b91c1c"}]]},layout:{hLineWidth:()=>0,vLineWidth:()=>0,fillColor:f?"#ecfdf5":"#fef2f2"},margin:[0,5,0,5]}),f){let h=[];k.files.forEach((b,N)=>{if(b.b64&&h.push({stack:[{image:b.b64,width:100,height:90,alignment:"center"},{text:b.name,fontSize:5,alignment:"center",color:"#9ca3af",margin:[0,2,0,0]}],width:"*"}),(h.length===4||N===k.files.length-1)&&h.length>0){for(;h.length<4;)h.push({text:"",width:"*"});x.content.push({columns:[...h],columnGap:10,margin:[0,5,0,10]}),h=[]}})}else k.files.forEach(h=>{const b=`${window.location.origin}/storage/files/documentUser/BankSampah/RT0${p.IDRT}/${h.name}`;x.content.push({text:[{text:"  • ",color:"#b91c1c",bold:!0},{text:h.name,color:"#2563eb",decoration:"underline",link:b,fontSize:8},{text:" (Klik untuk buka)",fontSize:7,color:"#9ca3af",italics:!0}],margin:[10,2,0,2]})})}))},setTimeout(()=>{const x=$.fn.dataTable.ext.buttons.pdfHtml5;typeof x.action=="function"?x.action.call(u,r,e,a,l):(console.error("Fungsi PDF asli tidak ditemukan"),g.close())},300)}},{extend:"excelHtml5",text:'<i class="fa-solid fa-file-excel mr-2"></i> Excel',className:"export-btn bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm",title:"Dokumen "+d.value+" SiBanksa RT"+(R.props.auth.user.user_detail?.id_rt||"-")+" Tanggal "+new Date().toLocaleDateString("id-ID").replace(/\//g,"-"),exportOptions:{columns:":not(.no-print)"},action:async function(r,e,a,l){const u=this;g.fire({title:"Memproses Excel...",text:"Menyiapkan lampiran",allowOutsideClick:!1,didOpen:()=>g.showLoading()}),l.customize=function(m){g.close();var w=m.xl.worksheets["sheet1.xml"];$("row c",w).attr("s","25"),$("row:first c",w).attr("s","51"),$('row:gt(0) c[r^="B"]',w).attr("s","21")},setTimeout(()=>{$.fn.dataTable.ext.buttons.excelHtml5.action.call(u,r,e,a,l)},300)}},{extend:"print",text:'<i class="fa-solid fa-print mr-2"></i> Print',className:"export-btn bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-md text-sm shadow-sm",title:"",action:async function(r,e,a,l){const u=this;g.fire({title:"Memproses Cetak...",text:"Menyiapkan lampiran",allowOutsideClick:!1,didOpen:()=>g.showLoading()}),l.customize=function(m){g.close();const w=F.value.map((D,x)=>{const f=d.value==="Document"?D.document.map(c=>`
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:5px; font-size:10px; color:#dc2626; background:#fef2f2; padding:6px; border-radius:6px; border:1px solid #fee2e2;">
                            <i class="fas fa-file-pdf"></i>
                            <span style="font-weight:600;">${c.original_filesname}</span>
                        </div>
                    `).join(""):D.photos.map(c=>`
                        <div style="display:inline-block; text-align:center; margin-right:12px; margin-bottom:12px;">
                            <img src="/storage/photo/evidenceUser/BankSampah/RT0${p.IDRT}/${c.original_photoname}"
                                 style="width:90px; height:90px; object-fit:cover; border-radius:8px; border:1px solid #e5e7eb; display:block; margin-bottom:5px;">
                            <span style="font-size:9px; color:#6b7280; display:block; max-width:90px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                ${c.original_photoname}
                            </span>
                        </div>
                    `).join("");return`
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td style="padding:15px; text-align:center; vertical-align:top; font-size:12px; color:#9ca3af;">${x+1}</td>
                        <td style="padding:15px; vertical-align:top;">
                            <div style="font-weight:bold; color:#111827; font-size:14px; margin-bottom:4px; text-transform:uppercase;">${D.name}</div>
                            <div style="font-size:11px; color:#10b981; font-weight:600; margin-bottom:15px;">Jadwal: ${D.tanggal_setoran}</div>
                            <div style="display:flex; flex-wrap:wrap;">
                                ${f||'<em style="color:#d1d5db; font-size:11px;">Tidak ada lampiran</em>'}
                            </div>
                        </td>
                        <td style="padding:15px; text-align:center; vertical-align:top;">
                            <span style="background:#f0fdf4; color:#166534; padding:4px 10px; border-radius:12px; font-size:10px; font-weight:800; border:1px solid #bbf7d0;">
                                ${d.value==="Document"?D.document.length:D.photos.length} FILE
                            </span>
                        </td>
                    </tr>
                `}).join("");$(m.document.body).css("font-family","'Poppins', sans-serif").prepend(`
                <div style="padding: 40px; border-top: 12px solid #10b981; background: #fff;">
                       <div
                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-[0.03] pointer-events-none">
                    <i class="fas fa-recycle text-[20rem]"></i>
                </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #f3f4f6; padding-bottom: 20px; margin-bottom: 30px;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div
                            class="w-16 h-16 bg-emerald-600 rounded-xl flex items-center justify-center text-white text-3xl shadow-lg">
                            <i class="fas fa-leaf"></i>
                        </div>
                    <div>
                        <h1 style="margin: 0; font-size: 24px; font-weight: 900; color: #1f2937;">SiBanksa</h1>
                        <p style="margin: 0; font-size: 10px; color: #6b7280; font-weight: bold; letter-spacing: 1px;">SISTEM INFORMASI BANK SAMPAH</p>
                    </div>
                </div>
                <div style="text-align: right;">
                    <h2 style="margin: 0; font-size: 28px; color: #d1d5db; letter-spacing: 4px;">LAPORAN KETUA RW</h2>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; margin-bottom: 40px; font-size: 14px;">
                <div>
                    <p style="color: #9ca3af; font-weight: bold; font-size: 10px; margin-bottom: 5px;">DITERIMA DARI:</p>
                    <p style="font-weight: bold; font-size: 18px; margin: 0;">${R.props.auth.user.user_detail.fullName}</p>
                    <p style="color: #6b7280; margin: 0;">${R.props.auth.user.user_detail.roles.role} SiBanksa</p>
                    <p style="color: #6b7280; margin: 0;">RT: ${R.props.auth.user.user_detail?.id_rt||"-"} / RW: 01</p>
                </div>
                <div style="text-align: right;">
                    <p style="color: #9ca3af; font-weight: bold; font-size: 10px; margin-bottom: 5px;">Dicetak Pada:</p>
                    <p style="font-weight: bold; font-size: 18px; margin: 0;">${new Date().toLocaleDateString("id-ID")}</p>
                    <p style="color: #6b7280; margin: 0;">Lokasi: Unit Bank Sampah RT-0${R.props.auth.user.user_detail?.id_rt||"-"}</p>
                </div>
            </div>
                    <table style="width:100%; border-collapse:collapse; margin-bottom:40px;">
                        <thead>
                            <tr style="background:#f9fafb; text-align:left; font-size:11px; color:#4b5563; text-transform:uppercase; letter-spacing:1px;">
                                <th style="padding:15px; width:40px; text-align:center; border-bottom:2px solid #eee;">No</th>
                                <th style="padding:15px; border-bottom:2px solid #eee;">Rincian Pelaksanaan & Bukti</th>
                                <th style="padding:15px; width:100px; text-align:center; border-bottom:2px solid #eee;">Status</th>
                            </tr>
                        </thead>
                        <tbody>${w}</tbody>
                    </table>
                    <div style="display:flex; justify-content:flex-end; margin-top:50px;">
                        <div style="text-align:center; width:250px;">
                            <p style="font-size:13px; margin-bottom:80px;">Ketua Bank Sampah RT-0${p.IDRT},</p>
                            <p style="font-weight:900; font-size:15px; text-decoration:underline; margin:0; text-transform:uppercase;">${R.props.auth.user.user_detail.fullName}</p>
                            <p style="font-size:11px; color:#9ca3af; margin:0;">NIP: SBK-RT0${p.IDRT}${new Date().getFullYear()}</p>
                        </div>
                    </div>
                </div>
            `),$(m.document.body).find("table").last().hide()},setTimeout(()=>{$.fn.dataTable.ext.buttons.print.action.call(u,r,e,a,l)},300)}}],language:{info:"Menampilkan _START_ - _END_ dari _TOTAL_ data",paginate:{previous:"← Sebelumnya",next:"Berikutnya →"},emptyTable:d.value==="Document"?`<div class="flex flex-col items-center  justify-center rounded-2xl shadow-inner">
  <div class="relative animate-pulse">
    <svg
  class="w-28 h-28 text-gray-400 dark:text-gray-500"
  viewBox="0 0 100 100"
  fill="none"
  xmlns="http://www.w3.org/2000/svg"
>
  <!-- Bingkai tabel -->
  <rect x="12" y="20" width="62" height="50" rx="6" stroke="currentColor" stroke-width="2" />

  <!-- Garis pemisah header -->
  <line x1="12" y1="34" x2="74" y2="34" stroke="currentColor" stroke-width="2" />

  <!-- Garis pemisah kolom -->
  <line x1="34" y1="20" x2="34" y2="70" stroke="currentColor" stroke-width="1.5" />
  <line x1="56" y1="20" x2="56" y2="70" stroke="currentColor" stroke-width="1.5" />

  <!-- Label header (judul kolom) -->
  <line x1="17" y1="27" x2="29" y2="27" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
  <line x1="39" y1="27" x2="51" y2="27" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
  <line x1="61" y1="27" x2="69" y2="27" stroke="currentColor" stroke-width="2" stroke-linecap="round" />

  <!-- Baris kosong (putus-putus, makin redup ke bawah) -->
  <line x1="17" y1="46" x2="29" y2="46" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.45" />
  <line x1="39" y1="46" x2="51" y2="46" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.45" />
  <line x1="61" y1="46" x2="69" y2="46" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.45" />

  <line x1="17" y1="58" x2="29" y2="58" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.3" />
  <line x1="39" y1="58" x2="51" y2="58" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.3" />
  <line x1="61" y1="58" x2="69" y2="58" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.3" />

  <!-- Kaca pembesar di pojok kanan bawah: menandakan "dicari, tidak ditemukan" -->
  <circle cx="78" cy="72" r="13" stroke="currentColor" stroke-width="2.5" />
  <line x1="87" y1="81" x2="95" y2="89" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" />
  <line x1="73" y1="72" x2="83" y2="72" stroke="currentColor" stroke-width="2" stroke-linecap="round" />

  <!-- Aksen dekoratif kecil, senada dengan ikon utama -->
  <circle cx="8" cy="10" r="2" stroke="currentColor" stroke-width="1.3" />
  <circle cx="91" cy="13" r="1.6" fill="currentColor" />
  <path d="M5 80 L9 80" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
</svg>
  </div>

  <h1 class="text-xl capitalize font-extrabold text-gray-800 dark:text-gray-100 tracking-tight mb-2">
    Maaf! Belum ada dokumen yang diupload.
  </h1>
   <p class="text-sm text-gray-600 dark:text-gray-300 max-w-lg mx-auto">
    Silahkan klik tombol <span class="text-emerald-500">Tambah Dokumen</span> untuk melaporkan dokumen hasil setoran.
  </p>
</div>`:`<div class="flex flex-col items-center  justify-center rounded-2xl shadow-inner">
  <div class="relative animate-pulse">
    <svg
  class="w-28 h-28 text-gray-400 dark:text-gray-500"
  viewBox="0 0 100 100"
  fill="none"
  xmlns="http://www.w3.org/2000/svg"
>
  <!-- Bingkai tabel -->
  <rect x="12" y="20" width="62" height="50" rx="6" stroke="currentColor" stroke-width="2" />

  <!-- Garis pemisah header -->
  <line x1="12" y1="34" x2="74" y2="34" stroke="currentColor" stroke-width="2" />

  <!-- Garis pemisah kolom -->
  <line x1="34" y1="20" x2="34" y2="70" stroke="currentColor" stroke-width="1.5" />
  <line x1="56" y1="20" x2="56" y2="70" stroke="currentColor" stroke-width="1.5" />

  <!-- Label header (judul kolom) -->
  <line x1="17" y1="27" x2="29" y2="27" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
  <line x1="39" y1="27" x2="51" y2="27" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
  <line x1="61" y1="27" x2="69" y2="27" stroke="currentColor" stroke-width="2" stroke-linecap="round" />

  <!-- Baris kosong (putus-putus, makin redup ke bawah) -->
  <line x1="17" y1="46" x2="29" y2="46" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.45" />
  <line x1="39" y1="46" x2="51" y2="46" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.45" />
  <line x1="61" y1="46" x2="69" y2="46" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.45" />

  <line x1="17" y1="58" x2="29" y2="58" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.3" />
  <line x1="39" y1="58" x2="51" y2="58" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.3" />
  <line x1="61" y1="58" x2="69" y2="58" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.3" />

  <!-- Kaca pembesar di pojok kanan bawah: menandakan "dicari, tidak ditemukan" -->
  <circle cx="78" cy="72" r="13" stroke="currentColor" stroke-width="2.5" />
  <line x1="87" y1="81" x2="95" y2="89" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" />
  <line x1="73" y1="72" x2="83" y2="72" stroke="currentColor" stroke-width="2" stroke-linecap="round" />

  <!-- Aksen dekoratif kecil, senada dengan ikon utama -->
  <circle cx="8" cy="10" r="2" stroke="currentColor" stroke-width="1.3" />
  <circle cx="91" cy="13" r="1.6" fill="currentColor" />
  <path d="M5 80 L9 80" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
</svg>
  </div>

  <h1 class="text-xl capitalize font-extrabold text-gray-800 dark:text-gray-100 tracking-tight mb-2">
    Maaf! Belum ada evidence yang diupload.
  </h1>
   <p class="text-sm text-gray-600 dark:text-gray-300 max-w-lg mx-auto">
    Silahkan klik tombol <span class="text-emerald-500">Tambah Evidence</span> untuk melaporkan evidence pelaksanaan.
  </p>
</div>`}})),ee=r=>{E.value.dt.search(r.target.value).draw()},te=r=>{const e=r.target.value;d.value,E.value.dt.column(1).search(e,!0,!1).draw()},ae=r=>{E.value.dt.page.len(parseInt(r.target.value)).draw()},L=r=>{E.value.dt.button(r).trigger()},fe=()=>{P.value=!1,o.reset(),j.value=!j.value},o=ye({name:d.value==="Document"?"":[],id_userdetail:p.IDUser,id_jadwal:"",fileDoc:[],imgEvidence:[]}),re=()=>{const r=P.value?d.value==="Document"?route("update-document",o.id):route("update-evidence",o.id):d.value==="Document"?route("add-document"):route("add-evidence"),e=P.value?"put":"post";o[e](r,{forceFormData:!0,onSuccess:()=>{P.value?d.value==="Document"?g.fire("Berhasil!","Dokumen telah diubah.","success"):g.fire("Berhasil!","Evidence telah diubah.","success"):d.value==="Document"?g.fire("Berhasil!","Dokumen telah disimpan.","success"):g.fire("Berhasil!","Evidence telah disimpan.","success"),j.value=!1,o.reset()},onError:function(a){if(a.status===422){const l=a.responseJSON.errors;let u="",m=0;Object.keys(l).forEach(w=>{l[w].forEach(D=>{u+=` <li class="text-[11px] text-red-600 dark:text-red-400 flex items-center gap-2">
                           <span class="w-1 h-1 bg-red-400 rounded-full"></span>
                           ${D}
                       </li>`,m++}),$(`[name="${w}"]`).addClass("border-red-500 ring-1 ring-red-500")}),$("#error-count").text(m),$("#error-list").html(u),$("#error-message").removeClass("hidden").fadeIn(),g.fire("Gagal!","Silakan periksa kembali inputan Anda.","error")}else g.fire("Gagal!","Silakan periksa kembali inputan Anda.","error")}})},oe=r=>{d.value=r,ue.value=1,o.clearErrors()},se=G(()=>d.value==="Document"?o.fileDoc.map((r,e)=>{const a=r.name.split(".").pop();return{original:r.name,dynamic:`Berkas ${o.name||"Dokumen"}_BankSampahRT0${p.IDRT}_${e+1}.${a}`,size:r.size}}):o.imgEvidence.map((r,e)=>{const a=r.name.split(".").pop();return{original:r.name,dynamic:`Evidence_${o.name||"Dokumen"}_BankSampahRT0${p.IDRT}_${e+1}.${a}`,size:r.size}})),F=G(()=>{const r={},e=["ktp","kk","akta","kartu keluarga"];return d.value==="Document"?p.document.forEach(a=>{if(e.some(m=>a.name.toLowerCase().includes(m)))return;const u=a.id_jadwal;if(!r[u]){const m=p.jadwalPelaksanaan.find(w=>w.id===u);r[u]={id_jadwal:u,name:a.name,tanggal_setoran:m?m.tanggal_setoran:"Tanggal Tidak Ditemukan",document:[]}}r[u].document.push(a)}):p.image.forEach(a=>{const l=a.name;if(!r[l]){const u=p.jadwalPelaksanaan.find(m=>m.tanggal_setoran===a.name);r[l]={name:a.name,id_userdetail:a.id_userdetail,tanggal_setoran:u?u.tanggal_setoran:"Tanggal Tidak Ditemukan",photos:[]}}r[l].photos.push(a)}),Object.values(r)}),ke=r=>{j.value=!1,g.fire({title:"Kirim Pengingat?",text:"Ketua RW akan menerima notifikasi mengenai pelaporan anda",icon:"question",showCancelButton:!0,confirmButtonColor:"#ef4444",confirmButtonText:"Ya, Kirim!"}).then(e=>{e.isConfirmed&&W.post(route("laporsetoran.send-reminder",r),{message:`Pelaporan Baru Hasil setoran pelaksanaan tanggal ${F.value.map(a=>a.tanggal_setoran).join(", ")} dari Bank Sampah RT0${p.IDRT}`},{onSuccess:()=>g.fire("Terkirim!","Pesan pengingat telah dikirim.","success")})})},he=[{label:"Dashboard",url:route("dashboard")},{label:"Manajemen Bank Sampah",url:null},{label:"Data Pelaporan Ketua RW",url:route("data-pelaporanRW")}];return(r,e)=>(s(),n(v,null,[M(i(ve),{title:"Data Pelaporan Upload Dokumen"}),M($e,{sidebardata:B.sidebardata,"breadcrumb-items":he},{default:z(()=>[t("div",Te,[t("div",Be,[e[20]||(e[20]=t("div",null,[t("h2",{class:"text-2xl font-bold text-gray-800 dark:text-white"},"Manajemen Data Pelaporan Pelaksanaan Bank Sampah"),t("p",{class:"text-sm text-gray-500 dark:text-gray-400"},"Kelola dokumen dan bukti pelaksanaan bank sampah Anda.")],-1)),t("div",je,[p.document.length>0&&p.image.length>0?(s(),n("div",Ee,[i(R).props.auth.user.user_detail.status_transaction!=="Disetujui"?(s(),n("button",{key:0,onClick:e[0]||(e[0]=a=>ke(p.IDRW)),class:"flex h-full items-center gap-2 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20"},[...e[18]||(e[18]=[t("i",{class:"fas fa-bell"},null,-1),C(" Ajukan Persetujuan Ketua RW ",-1)])])):S("",!0)])):(s(),n("div",Re,[e[19]||(e[19]=t("div",{class:"flex-shrink-0"},[t("svg",{class:"w-5 h-5 text-red-500",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"})])],-1)),t("div",ze,[p.document.length==0?(s(),n("span",Pe,"Belum upload hasil setoran")):p.image.length==0?(s(),n("span",Ie,"Belum upload bukti evidence")):(s(),n("span",Ae,"Mohon lengkapi bukti upload Anda"))])])),t("button",{onClick:fe,class:_(["text-white px-5 py-2.5 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-500/20 active:scale-95",[j.value?"bg-red-500 hover:bg-red-600 shadow-red-500/20":"bg-emerald-500 hover:bg-emerald-600 shadow-emerald-500/20"]])},[t("i",{class:_(["fas",j.value?"fa-times":"fa-plus"])},null,2),C(" "+y(j.value?"Tutup Form":d.value==="Evidence"?"Tambah Evidence":"Tambah Dokumen"),1)],2)])]),t("div",Le,[t("button",{onClick:e[1]||(e[1]=a=>oe("Document")),class:_([d.value==="Document"?"bg-white shadow-md text-emerald-600":"text-gray-500","flex-1 py-3 rounded-xl transition-all font-semibold text-sm"])}," Document ",2),t("button",{onClick:e[2]||(e[2]=a=>oe("Evidence")),class:_([d.value==="Evidence"?"bg-white shadow-md text-emerald-600":"text-gray-500","flex-1 py-3 rounded-xl transition-all font-semibold text-sm"])}," Evidence ",2)]),M(we,{name:"accordion"},{default:z(()=>[j.value?(s(),n("div",Me,[t("h3",Fe,y(P.value?"Perbarui Data":"Input Data Baru"),1),t("div",Ne,[d.value==="Document"?(s(),O(le,{key:0,formName:"formDocument",errors:i(o).errors,processing:i(o).processing,onSubmit:re},{default:z(()=>[U(t("input",{type:"hidden",name:"id_userdetail","onUpdate:modelValue":e[3]||(e[3]=a=>i(o).id_userdetail=a)},null,512),[[ie,i(o).id_userdetail]]),t("div",{class:_(["grid grid-cols-1 gap-4",i(o).type==="file"?"md:grid-cols-1":"md:grid-cols-2"])},[t("div",Ue,[e[22]||(e[22]=t("label",{class:"block mb-1 text-[11px] font-bold text-gray-400 uppercase dark:text-gray-300"},"Jadwal Pelaksanaan",-1)),U(t("select",{onChange:e[4]||(e[4]=(...a)=>r.handleScheduleChange&&r.handleScheduleChange(...a)),"onUpdate:modelValue":e[5]||(e[5]=a=>i(o).id_jadwal=a),class:_(["text-black w-full h-11 rounded-xl bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 dark:text-white text-sm pl-5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm",{"border-red-500 ring-1 ring-red-500":i(o).errors.id_jadwal}])},[e[21]||(e[21]=t("option",{value:"",disabled:""},"Pilih Jadwal",-1)),(s(!0),n(v,null,T(B.jadwalPelaksanaan,a=>(s(),n("option",{key:a.id,value:a.id},y(a.tanggal_setoran),9,Ke))),128))],34),[[J,i(o).id_jadwal]])]),(s(!0),n(v,null,T(B.formdata.Dokumen,a=>(s(),n(v,{key:a.name},[a.name==="name"?(s(),n("div",Oe,[M(q,{for:a.name,value:a.title},null,8,["for","value"]),U(t("select",{"onUpdate:modelValue":e[6]||(e[6]=l=>i(o).name=l),class:_(["w-full h-11 rounded-xl dark:text-white text-black bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:ring-emerald-500 transition-all shadow-sm text-sm pl-5",{"border-red-500 ring-1 ring-red-500":i(o).errors[a.name]}])},[e[23]||(e[23]=t("option",{value:""},"Pilih Jenis Upload",-1)),(s(!0),n(v,null,T(a.options,l=>(s(),n("option",{key:l,value:l},y(l),9,He))),128))],2),[[J,i(o).name]])])):S("",!0)],64))),128)),(s(!0),n(v,null,T(B.formdata.Dokumen,a=>(s(),n(v,{key:a.name},[a.type==="file"&&a.name==="fileDoc"?(s(),n("div",Ve,[M(q,{for:a.name,value:a.title},null,8,["for","value"]),t("input",{type:a.type,id:a.name,multiple:"",onInput:e[7]||(e[7]=l=>{const u=Array.from(l.target.files);i(o).fileDoc=[...i(o).fileDoc,...u]}),placeholder:a.placeholder,class:_(["w-full h-11 rounded-xl text-black bg-gray-50 dark:bg-gray-800 dark:text-white pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm border-gray-200",{"border-red-500 ring-1 ring-red-500":i(o).errors[a.name]}])},null,42,Je),i(o)[a.name]?.length?(s(),n("p",We,y(i(o)[a.name].length)+" file terpilih ",1)):S("",!0),i(o).fileDoc.length>0?(s(),n("ul",Ge,[(s(!0),n(v,null,T(se.value,(l,u)=>(s(),n("li",{key:u,class:"text-xs text-gray-500 flex items-center justify-between bg-gray-50 dark:bg-gray-800 p-2 rounded-lg"},[t("div",qe,[e[24]||(e[24]=t("svg",{class:"w-3 h-3 mr-1 text-emerald-500",fill:"currentColor",viewBox:"0 0 20 20"},[t("path",{d:"M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"})],-1)),C(" "+y(l.dynamic)+" ("+y((l.size/1024).toFixed(1))+" KB) ",1)]),t("button",{type:"button",onClick:m=>ge(u),class:"text-red-500 hover:text-red-700 p-1"},[...e[25]||(e[25]=[t("svg",{class:"w-4 h-4",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"})],-1)])],8,Ye)]))),128))])):S("",!0)])):S("",!0)],64))),128))],2),t("div",Ze,[t("button",{type:"submit",class:"bg-emerald-500 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-emerald-600 transition disabled:opacity-50",disabled:i(o).processing},[e[26]||(e[26]=t("i",{class:"fas fa-save mr-2"},null,-1)),C(" "+y(P.value?"Update Dokumen":"Simpan Dokumen"),1)],8,Qe)])]),_:1},8,["errors","processing"])):(s(),O(le,{key:1,formName:"formEvidence",errors:i(o).errors,processing:i(o).processing,onSubmit:re},{default:z(()=>[U(t("input",{type:"hidden",name:"id_userdetail","onUpdate:modelValue":e[8]||(e[8]=a=>i(o).id_userdetail=a)},null,512),[[ie,i(o).id_userdetail]]),t("div",Xe,[t("div",et,[e[28]||(e[28]=t("label",{class:"block mb-1 text-[11px] font-bold text-gray-400 uppercase dark:text-gray-300"},"Jadwal Pelaksanaan",-1)),U(t("select",{onChange:e[9]||(e[9]=(...a)=>r.handleScheduleChange&&r.handleScheduleChange(...a)),"onUpdate:modelValue":e[10]||(e[10]=a=>i(o).name=a),class:_(["text-black w-full h-11 rounded-xl bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 dark:text-white text-sm pl-5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm",{"border-red-500 ring-1 ring-red-500":i(o).errors.id_jadwal}])},[e[27]||(e[27]=t("option",{value:"",disabled:""},"Pilih Jadwal",-1)),(s(!0),n(v,null,T(B.jadwalPelaksanaan,a=>(s(),n("option",{key:a.id,value:a.tanggal_setoran},y(a.tanggal_setoran),9,tt))),128))],34),[[J,i(o).name]])]),(s(!0),n(v,null,T(B.formdata.Dokumen,a=>(s(),n(v,{key:a.name},[a.type==="file"&&a.name==="imgEvidence"?(s(),n("div",at,[M(q,{for:a.name,value:a.title},null,8,["for","value"]),t("input",{type:a.type,id:a.name,multiple:"",onInput:e[11]||(e[11]=l=>{const u=Array.from(l.target.files);i(o).imgEvidence=[...i(o).imgEvidence,...u]}),placeholder:a.placeholder,class:_(["w-full h-11 rounded-xl bg-gray-50 dark:bg-gray-800 text-black e dark:text-white pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm border-gray-200",{"border-red-500 ring-1 ring-red-500":i(o).errors[a.name]}])},null,42,rt),i(o)[a.name]?.length?(s(),n("p",ot,y(i(o)[a.name].length)+" file terpilih ",1)):S("",!0),i(o).imgEvidence.length>0?(s(),n("ul",st,[(s(!0),n(v,null,T(se.value,(l,u)=>(s(),n("li",{key:u,class:"text-xs text-gray-500 flex items-center justify-between bg-gray-50 dark:bg-gray-800 p-2 rounded-lg"},[t("div",nt,[e[29]||(e[29]=t("svg",{class:"w-3 h-3 mr-1 text-emerald-500",fill:"currentColor",viewBox:"0 0 20 20"},[t("path",{d:"M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"})],-1)),C(" "+y(l.dynamic)+" ("+y((l.size/1024).toFixed(1))+" KB) ",1)]),t("button",{type:"button",onClick:m=>xe(u),class:"text-red-500 hover:text-red-700 p-1"},[...e[30]||(e[30]=[t("svg",{class:"w-4 h-4",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"})],-1)])],8,lt)]))),128))])):S("",!0)])):S("",!0)],64))),128))]),t("div",it,[t("button",{type:"submit",class:"bg-emerald-500 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-emerald-600 transition disabled:opacity-50",disabled:i(o).processing},[e[31]||(e[31]=t("i",{class:"fas fa-save mr-2"},null,-1)),C(" "+y(P.value?"Update Evidence":"Simpan Evidence"),1)],8,dt)])]),_:1},8,["errors","processing"]))])])):S("",!0)]),_:1}),t("div",ut,[d.value==="Document"?(s(),n("div",ct,[t("div",pt,[t("button",{onClick:e[12]||(e[12]=a=>L(0)),class:"flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm"},[...e[32]||(e[32]=[t("i",{class:"fas fa-file-pdf"},null,-1),C(" PDF ",-1)])]),t("button",{onClick:e[13]||(e[13]=a=>L(1)),class:"flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm"},[...e[33]||(e[33]=[t("i",{class:"fas fa-file-excel"},null,-1),C(" Excel ",-1)])]),t("button",{onClick:e[14]||(e[14]=a=>L(2)),class:"flex items-center gap-2 bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm"},[...e[34]||(e[34]=[t("i",{class:"fas fa-print"},null,-1),C(" Print ",-1)])])]),t("div",mt,[t("div",gt,[e[35]||(e[35]=t("label",{class:"text-xs m-auto font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"},"Cari:",-1)),t("input",{onKeyup:ee,type:"text",class:"border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none w-40 transition-all",placeholder:"Ketik..."},null,32)]),t("div",xt,[e[37]||(e[37]=t("label",{class:"text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"},"Kategori:",-1)),t("select",{onChange:te,class:"border border-gray-200 dark:border-gray-600 text-black rounded-lg px-2 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer"},[...e[36]||(e[36]=[t("option",{value:""},"Semua",-1),t("option",{value:"Hasil Setoran"},"Hasil Setoran",-1),t("option",{value:"Dokumen Lainnya"},"Dokumen Lainnya",-1)])],32)]),t("div",ft,[e[39]||(e[39]=t("label",{class:"text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"},"Show:",-1)),t("select",{onChange:ae,class:"bg-transparent text-sm font-bold text-gray-700 dark:text-gray-200 focus:outline-none cursor-pointer"},[...e[38]||(e[38]=[t("option",{value:"5",selected:""},"5",-1),t("option",{value:"10"},"10",-1),t("option",{value:"25"},"25",-1)])],32)])])])):(s(),n("div",kt,[t("div",ht,[t("button",{onClick:e[15]||(e[15]=a=>L(0)),class:"flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm"},[...e[40]||(e[40]=[t("i",{class:"fas fa-file-pdf"},null,-1),C(" PDF ",-1)])]),t("button",{onClick:e[16]||(e[16]=a=>L(1)),class:"flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm"},[...e[41]||(e[41]=[t("i",{class:"fas fa-file-excel"},null,-1),C(" Excel ",-1)])]),t("button",{onClick:e[17]||(e[17]=a=>L(2)),class:"flex items-center gap-2 bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm"},[...e[42]||(e[42]=[t("i",{class:"fas fa-print"},null,-1),C(" Print ",-1)])])]),t("div",bt,[t("div",yt,[e[43]||(e[43]=t("label",{class:"text-xs m-auto font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"},"Cari:",-1)),t("input",{onKeyup:ee,type:"text",class:"border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none w-40 transition-all",placeholder:"Ketik..."},null,32)]),t("div",vt,[e[45]||(e[45]=t("label",{class:"text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"},"Kategori:",-1)),t("select",{onChange:te,class:"border border-gray-200 text-black dark:border-gray-600 rounded-lg px-2 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer"},[e[44]||(e[44]=t("option",{value:""},"Semua",-1)),(s(!0),n(v,null,T(B.jadwalPelaksanaan,a=>(s(),n("option",{key:a.id,value:a.tanggal_setoran},y(a.tanggal_setoran),9,wt))),128))],32)]),t("div",Dt,[e[47]||(e[47]=t("label",{class:"text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"},"Show:",-1)),t("select",{onChange:ae,class:"bg-transparent text-sm font-bold text-gray-700 dark:text-gray-200 focus:outline-none cursor-pointer"},[...e[46]||(e[46]=[t("option",{value:"5",selected:""},"5",-1),t("option",{value:"10"},"10",-1),t("option",{value:"25"},"25",-1)])],32)])])])),t("div",Ct,[d.value==="Document"?(s(),O(i(A),{key:0,data:F.value,ref_key:"dtInstance",ref:E,options:X.value,class:"w-full display stripe hover cell-border dark:text-white"},{"column-0":z(a=>[t("div",{class:"flex justify-center gap-2"},[t("button",{onClick:Q,class:"p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition",title:"Edit"},[...e[48]||(e[48]=[t("i",{class:"fas fa-plus-circle text-emerald-500 cursor-pointer"},null,-1)])])])]),default:z(()=>[e[49]||(e[49]=t("thead",{class:"text-xs text-gray-700 uppercase dark:text-gray-400"},[t("tr",null,[t("th"),t("th",{class:"pb-4 font-semibold uppercase text-[12px] tracking-wider text-center"},"Nama Dokumen"),t("th",{class:"pb-4 font-semibold uppercase text-[12px] tracking-wider text-center"},"Tanggal Pelaksanaan"),t("th",{class:"pb-4 font-semibold uppercase text-[12px] tracking-wider text-center"},"Dokumen")])],-1))]),_:1},8,["data","options"])):(s(),O(i(A),{key:1,data:F.value,ref_key:"dtInstance",ref:E,options:X.value,class:"w-full display stripe hover cell-border dark:text-white"},{"column-0":z(a=>[t("div",{class:"flex justify-center gap-2"},[t("button",{onClick:Q,class:"p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition",title:"Edit"},[...e[50]||(e[50]=[t("i",{class:"fas fa-plus-circle text-emerald-500 cursor-pointer"},null,-1)])])])]),default:z(()=>[e[51]||(e[51]=t("thead",{class:"text-xs text-gray-700 uppercase dark:text-gray-400"},[t("tr",null,[t("th"),t("th",{class:"pb-4 font-semibold uppercase text-[12px] tracking-wider text-center"},"Nama Evidence"),t("th",{class:"pb-4 font-semibold uppercase text-[12px] tracking-wider text-center"},"Evidence")])],-1))]),_:1},8,["data","options"]))])])])]),_:1},8,["sidebardata"]),H.value?(s(),n("div",{key:0,class:"fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm",onClick:De(Z,["self"])},[t("div",$t,[t("button",{onClick:Z,class:"absolute -top-12 right-0 text-white hover:text-gray-300 transition-colors"},[...e[52]||(e[52]=[t("svg",{xmlns:"http://www.w3.org/2000/svg",class:"h-8 w-8",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M6 18L18 6M6 6l12 12"})],-1)])]),d.value==="Document"?(s(),n("div",St,[t("embed",{src:V.value,type:"application/pdf",class:"w-full h-full rounded-lg shadow-inner"},null,8,_t)])):(s(),n("div",Tt,[t("img",{src:V.value,class:"max-w-full max-h-full object-contain",alt:"Preview Image"},null,8,Bt)])),e[53]||(e[53]=t("p",{class:"mt-4 text-white text-sm font-medium"},"Klik di mana saja untuk menutup",-1))])])):S("",!0)],64))}};export{Ht as default};
