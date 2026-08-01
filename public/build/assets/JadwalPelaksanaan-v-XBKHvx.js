import{u as te,g as p,a as _,b as f,h as ae,w as D,d as e,n as z,f as c,t as g,T as re,o as u,_ as se,F as L,q as E,j as H,v as oe,x as le,i as j,y as ne,r as T,B as ie}from"./app-Clwanz8J.js";import{_ as de}from"./AuthenticatedLayout-B-lnY2kA.js";import{S as l}from"./sweetalert2.esm.all-BjNi2kua.js";import{_ as pe}from"./InputLabel-DzTPtl7U.js";import{j as ce,v as N,b as ue}from"./vfs_fonts-y7rtmNJR.js";import{V as C,D as B}from"./datatables.net-vue3-1beeUCbI.js";import"./responsive.dataTables-BwPvsgzI.js";/* empty css            */import"./_plugin-vue_export-helper-DlAUqK2U.js";import"./Preloader-WABfmdIk.js";import"./404-CrWFNT8x.js";import"./SessionExpired-CEpiEzIi.js";/* empty css                                                               */import"./_commonjs-dynamic-modules-TDtrdbi3.js";const ge={class:"space-y-6"},xe={class:"flex flex-col md:flex-row md:items-center justify-between gap-4"},me={key:0,class:"bg-white accordion-wrapper overflow-hidden dark:bg-gray-800 p-6 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700"},fe={class:"text-lg w-full font-semibold mb-4 text-black dark:text-white"},be={key:0,class:"col-span-full"},ye=["type","id","name","onUpdate:modelValue","placeholder"],he={class:"md:col-span-2 lg:col-span-3 flex justify-end items-center gap-3 pt-2"},ke=["disabled"],we={class:"bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700"},ve={class:"flex flex-col lg:flex-row lg:items-end justify-between mb-6"},_e={class:"flex flex-wrap mb-5 lg:mb-0 items-center gap-2"},De={class:"flex flex-wrap md:flex-nowrap items-end justify-start gap-3"},Ce={class:"flex items-end gap-2"},Se={class:"flex items-center gap-2 pl-0 md:pl-3"},je={class:"hidden md:block"},Te={class:"font-medium text-gray-700 dark:text-gray-200"},$e={class:"flex justify-center gap-1"},Be=["onClick"],Ie=["onClick"],ze={class:"block md:hidden space-y-4"},Le={key:0,class:"text-[10px] text-gray-500 font-bold uppercase mb-2"},Me={class:"flex justify-between items-start mb-3"},Ae={class:"flex items-center gap-3"},Ee={class:"w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-full flex items-center justify-center font-bold text-sm"},Ne={class:"font-bold text-gray-800 dark:text-white capitalize leading-tight"},Je={class:"text-[10px] text-gray-400 font-medium"},Pe={class:"grid grid-cols-3 gap-2 mt-4 pt-3 border-t border-gray-50 dark:border-gray-800"},Oe=["onClick"],Re=["onClick"],He={key:1,class:"flex flex-col items-center justify-center py-12 text-gray-400 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-800"},Fe={key:2,class:"mt-6 flex flex-col items-center gap-4"},Ve={class:"text-xs text-gray-500 font-medium"},Ke={class:"text-gray-800 dark:text-white font-bold"},Ue={class:"text-gray-800 dark:text-white font-bold"},Ge={class:"flex items-center gap-2 w-full"},We=["disabled"],qe=["disabled"],Ye={class:"flex gap-1.5"},ut={__name:"JadwalPelaksanaan",props:{jadwal:Array,formdata:Object,sidebardata:Object,idUser:Number,breadcrumbItems:Array},setup(I){C.use(B),C.use(B),C.use(B),C.use(B),C.use(B),window.JSZip=ce;const F=N.pdfMake?N.pdfMake.vfs:N.vfs;ue.vfs=F;const J=I,b=T(!1),w=T(!1),k=T(null),o=te({id:null,tanggal_setoran:"",id_userdetail:J.idUser}),y=ne(),S=T([]),x=T({page:0,pages:0,start:0,end:0,recordsDisplay:0}),V=r=>{r&&(S.value=r.rows({search:"applied",page:"current"}).data().toArray(),x.value=r.page.info())},K={pageLength:5,responsive:!0,lengthMenu:[5,10,25,50],drawCallback:function(){const r=this.api();V(r)},columns:[{data:null,render:(r,t,a,s)=>s.row+1},{data:"tanggal_setoran",render:(r,t,a)=>{const s=a.tanggal_setoran;return s?new Date(s).toLocaleDateString("id-ID",{day:"numeric",month:"long",year:"numeric"}):"-"},defaultContent:"-"},{data:null,orderable:!1,className:"no-print text-center"}],layout:{topStart:null,topEnd:null,bottomStart:"info",bottomEnd:"paging"},buttons:[{extend:"pdfHtml5",text:'<i class="fa-solid fa-file-pdf mr-2"></i> PDF',className:"export-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm",pageSize:"A4",title:"Laporan Jadwal Pelaksanaan SiBanksa RT"+(y.props.auth.user.user_detail?.id_rt||"-")+" Tanggal "+new Date().toLocaleDateString("id-ID").replace(/\//g,"-"),exportOptions:{columns:":not(.no-print)"},action:async function(r,t,a,s){const m=this;l.fire({title:"Memproses PDF...",text:"Menyiapkan lampiran",allowOutsideClick:!1,didOpen:()=>l.showLoading()}),s.customize=function(i){l.close();const d=i.content.find(n=>n.table);d&&(d.table.widths=[25,"*"],d.table.body.forEach((n,X)=>{n.forEach((Ze,h)=>{if((n[h]===void 0||n[h]===null)&&(n[h]={text:""}),X>0){let A="";typeof n[h]=="object"?A=n[h].text||"":A=n[h].toString();const R=A.toLowerCase().replace(/\b\w/g,ee=>ee.toUpperCase());typeof n[h]=="object"?n[h].text=R:n[h]=R}})}),d.layout="lightHorizontalLines");const v=y.props.auth.user?.user_detail;i.content.splice(0,1,{columns:[{stack:[{text:"SiBanksa",fontSize:22,bold:!0,color:"#10b981"},{text:"Sistem Informasi Bank Sampah Digital",fontSize:8,color:"#6b7280"}]},{stack:[{text:"LAPORAN JADWAL",fontSize:16,bold:!0,alignment:"right"},{text:`UNIT RT-0${v?.id_rt||"-"}`,fontSize:10,alignment:"right",color:"#9ca3af"}],width:"*"}]},{canvas:[{type:"line",x1:0,y1:5,x2:515,y2:5,lineWidth:1,lineColor:"#10b981"}],margin:[0,5,0,15]}),i.content.push({text:`

`},{columns:[{text:"",width:"*"},{width:180,stack:[{text:`Gresik, ${new Date().toLocaleDateString("id-ID",{day:"numeric",month:"long",year:"numeric"})}`,alignment:"center"},{text:"Verifikator Lapangan",alignment:"center",margin:[0,5,0,40]},{text:`( Ketua Bank Sampah RT-0${v?.id_rt||"-"} )`,alignment:"center",bold:!0},{text:"ID: SBK-RT0"+(v?.id_rt||"-"),alignment:"center",fontSize:8,color:"#9ca3af"}]}]}),i.styles.tableHeader={fillColor:"#10b981",color:"white",bold:!0,alignment:"center"}},setTimeout(()=>{$.fn.dataTable.ext.buttons.pdfHtml5.action.call(m,r,t,a,s)},300)}},{extend:"excelHtml5",text:'<i class="fa-solid fa-file-excel mr-2"></i> Excel',className:"export-btn bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm",title:"Laporan Jadwal Pelaksanaan SiBanksa RT-0"+(y.props.auth.user.user_detail?.id_rt||"-")+" Tanggal "+new Date().toLocaleDateString("id-ID").replace(/\//g,"-"),exportOptions:{columns:":not(.no-print)"},action:async function(r,t,a,s){const m=this;l.fire({title:"Memproses Excel...",text:"Menyiapkan lampiran",allowOutsideClick:!1,didOpen:()=>l.showLoading()}),s.customize=function(i){l.close();var d=i.xl.worksheets["sheet1.xml"];$("row c",d).attr("s","25"),$("row:first c",d).attr("s","51")},setTimeout(()=>{$.fn.dataTable.ext.buttons.excelHtml5.action.call(m,r,t,a,s)},300)}},{extend:"print",text:'<i class="fa-solid fa-print mr-2"></i> Print',className:"export-btn bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-md text-sm shadow-sm",title:"",action:async function(r,t,a,s){const m=this;l.fire({title:"Memproses Print...",text:"Menyiapkan lampiran",allowOutsideClick:!1,didOpen:()=>l.showLoading()}),s.customize=function(i){l.close();const d=J.jadwal?.map((v,n)=>`
        <tr style="border-bottom: 1px solid #f3f4f6; font-style: italic;">
            <td style="padding: 12px; font-weight: 600; color: #1f2937; text-transform: uppercase;">
               ${n+1}
            </td>
            <td  style="padding: 12px; font-weight: 600; color: #1f2937; text-transform: uppercase;">
                ${v.tanggal_setoran}
            </td>

        </tr>
    `).join("");$(i.document.body).css("font-family","Poppins, sans-serif").prepend(`
        <div style="padding: 40px; border-top: 10px solid #10b981; background: white;">
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
                    <h2 style="margin: 0; font-size: 28px; color: #d1d5db; letter-spacing: 4px;">JADWAL</h2>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; margin-bottom: 40px; font-size: 14px;">
                <div>
                    <p style="color: #9ca3af; font-weight: bold; font-size: 10px; margin-bottom: 5px;">DITERIMA DARI:</p>
                    <p style="font-weight: bold; font-size: 18px; margin: 0;">${y.props.auth.user.user_detail.fullName}</p>
                    <p style="color: #6b7280; margin: 0;">${y.props.auth.user.user_detail.roles.role} SiBanksa</p>
                    <p style="color: #6b7280; margin: 0;">RT: ${y.props.auth.user.user_detail?.id_rt||"-"} / RW: 01</p>
                </div>
                <div style="text-align: right;">
                    <p style="color: #9ca3af; font-weight: bold; font-size: 10px; margin-bottom: 5px;">Dicetak Pada:</p>
                    <p style="font-weight: bold; font-size: 18px; margin: 0;">${new Date().toLocaleDateString("id-ID")}</p>
                    <p style="color: #6b7280; margin: 0;">Lokasi: Unit Bank Sampah RT-0${y.props.auth.user.user_detail?.id_rt||"-"}</p>
                </div>
            </div>

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 40px;">
                <thead>
                    <tr style="background: #f9fafb; color: #6b7280; font-size: 10px; text-transform: uppercase; letter-spacing: 1px;">
                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: left;">No</th>
                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: left;">Jadwal Pelaksanaan</th>
                    </tr>
                </thead>
                <tbody>
                    ${d}
                </tbody>

            </table>

           <div style="display: flex; justify-content: flex-end; margin-top: 40px;">
                            <div style="text-align: center; width: 220px;">
                                <p style="font-size: 11px; margin-bottom: 60px;">Gresik, ${new Date().toLocaleDateString("id-ID",{day:"numeric",month:"long",year:"numeric"})}<br><b>Verifikator</b></p>
                                <div style="border-bottom: 1px solid #d1d5db; width: 180px; margin: 0 auto 5px;"></div>
                                <p style="font-weight: bold; font-size: 12px; text-transform: uppercase;">( Ketua Bank Sampah RT-0${y.props.auth.user.user_detail?.id_rt||"-"} )</p>
                                <p style="font-size: 9px; color: #9ca3af;">ID: SBK-RT0${y.props.auth.user.user_detail?.id_rt||"-"}</p>
                            </div>
                        </div>
        </div>
    `),$(i.document.body).find("table").last().hide()},setTimeout(()=>{$.fn.dataTable.ext.buttons.print.action.call(m,r,t,a,s)},300)}}],language:{info:"Menampilkan _START_ - _END_ dari _TOTAL_ data",paginate:{previous:"← Sebelumnya",next:"Berikutnya →"},emptyTable:`<div class="flex flex-col items-center  justify-center rounded-2xl shadow-inner">
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
    Maaf! Belum ada jadwal pelaksanaan.
  </h1>
   <p class="text-sm text-gray-600 dark:text-gray-300 max-w-lg mx-auto">
    Silahkan klik tombol <span class="text-emerald-500">Tambah Jadwal</span> untuk menambah jadwal baru.
  </p>
</div>`}},U=()=>{k.value?.dt&&k.value.dt.page("previous").draw("page")},G=()=>{k.value?.dt&&k.value.dt.page("next").draw("page")},W=r=>{k.value.dt.search(r.target.value).draw()},q=r=>{k.value.dt.page.len(parseInt(r.target.value)).draw()},M=r=>{k.value.dt.button(r).trigger()},Y=()=>{w.value=!1,o.reset(),b.value=!b.value},P=r=>{w.value=!0,o.id=r.id,o.tanggal_setoran=r.tanggal_setoran?r.tanggal_setoran.substring(0,10):"",o.id_userdetail=o.id_userdetail,b.value=!0,window.scrollTo({top:0,behavior:"smooth"})},Z=()=>{const r=w.value?route("update-jadwalBankSampah",o.id):route("add-jadwalBankSampah"),t=w.value?"put":"post";o[t](r,{onSuccess:()=>{w.value?l.fire("Berhasil!","Data jadwal berhasil diubah","success"):l.fire("Berhasil!","Data jadwal telah diproses.","success"),b.value=!1,o.reset()},onError:function(a){if(a.status===422){const s=a.responseJSON.errors;let m="",i=0;Object.keys(s).forEach(d=>{s[d].forEach(v=>{m+=` <li class="text-[11px] text-red-600 dark:text-red-400 flex items-center gap-2">
                           <span class="w-1 h-1 bg-red-400 rounded-full"></span>
                           ${v}
                       </li>`,i++}),$(`[name="${d}"]`).addClass("border-red-500 ring-1 ring-red-500")}),$("#error-count").text(i),$("#error-list").html(m),$("#error-message").removeClass("hidden").fadeIn(),l.fire("Gagal!","Silakan periksa kembali inputan Anda.","error")}else l.fire("Error",a.responseJSON?.message||"Maaf, Inputan Anda ada yang salah, silahkan cek kembali","error")}})},O=r=>{b.value=!1,l.fire({title:"Hapus data?",text:"Tindakan ini tidak bisa dibatalkan!",icon:"warning",showCancelButton:!0,confirmButtonColor:"#ef4444",confirmButtonText:"Ya, Hapus!"}).then(t=>{t.isConfirmed&&ie.delete(route("delete-jadwalBankSampah",r),{onSuccess:()=>l.fire("Dihapus!","Data berhasil dihapus.","success")})})},Q=[{label:"Dashboard",url:route("dashboard")},{label:"Manajemen Bank Sampah",url:null},{label:"Data Jadwal",url:route("jadwal-pelaksanaan")}];return(r,t)=>(u(),p(L,null,[_(f(ae),{title:"Data Jadwal"}),_(de,{sidebardata:I.sidebardata,breadcrumbItems:Q},{default:D(()=>[e("div",ge,[e("div",xe,[t[4]||(t[4]=e("div",null,[e("h2",{class:"text-2xl font-bold text-gray-800 dark:text-white"},"Manajemen Data Jadwal"),e("p",{class:"text-sm text-gray-500 dark:text-gray-400"},"Kelola daftar jadwal Anda.")],-1)),e("button",{onClick:Y,class:z(["text-white px-5 py-2.5 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-500/20 active:scale-95",[b.value?"bg-red-500 hover:bg-red-600 shadow-red-500/20":"bg-emerald-500 hover:bg-emerald-600 shadow-emerald-500/20"]])},[e("i",{class:z(["fas",b.value?"fa-times":"fa-plus"])},null,2),c(" "+g(b.value?"Tutup Form":"Tambah Jadwal"),1)],2)]),_(re,{name:"accordion"},{default:D(()=>[b.value?(u(),p("div",me,[e("h3",fe,g(w.value?"Perbarui Data":"Input Data Baru"),1),_(se,{formName:"formJadwal",errors:f(o).errors,processing:f(o).processing,onSubmit:Z},{default:D(()=>[(u(!0),p(L,null,E(I.formdata.bankSampah,a=>(u(),p("div",{key:a.name},[H(e("input",{type:"hidden",name:"id_userdetail","onUpdate:modelValue":t[0]||(t[0]=s=>f(o).id_userdetail=s)},null,512),[[oe,f(o).id_userdetail]]),a.type==="date"?(u(),p("div",be,[_(pe,{for:a.name,value:a.title},null,8,["for","value"]),H(e("input",{type:a.type,id:a.name,name:a.name,"onUpdate:modelValue":s=>f(o)[a.name]=s,placeholder:a.placeholder,class:z([{"border-red-500 ring-1 ring-red-500":f(o).errors[a.name]},"w-full h-11 rounded-xl text-black bg-gray-50 dark:bg-gray-800 dark:text-white pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all border-gray-200"])},null,10,ye),[[le,f(o)[a.name]]])])):j("",!0)]))),128)),e("div",he,[e("button",{type:"submit",class:"bg-emerald-500 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-emerald-600 transition disabled:opacity-50",disabled:f(o).processing},[t[5]||(t[5]=e("i",{class:"fas fa-save mr-2"},null,-1)),c(" "+g(w.value?"Update Jadwal":"Simpan Jadwal"),1)],8,ke)])]),_:1},8,["errors","processing"])])):j("",!0)]),_:1}),e("div",we,[e("div",ve,[e("div",_e,[e("button",{onClick:t[1]||(t[1]=a=>M(0)),class:"flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm"},[...t[6]||(t[6]=[e("i",{class:"fas fa-file-pdf"},null,-1),c(" PDF ",-1)])]),e("button",{onClick:t[2]||(t[2]=a=>M(1)),class:"flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm"},[...t[7]||(t[7]=[e("i",{class:"fas fa-file-excel"},null,-1),c(" Excel ",-1)])]),e("button",{onClick:t[3]||(t[3]=a=>M(2)),class:"flex items-center gap-2 bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm"},[...t[8]||(t[8]=[e("i",{class:"fas fa-print"},null,-1),c(" Print ",-1)])])]),e("div",De,[e("div",Ce,[t[9]||(t[9]=e("label",{class:"text-xs m-auto font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"},"Cari:",-1)),e("input",{onKeyup:W,type:"text",class:"border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none w-40 transition-all",placeholder:"Ketik..."},null,32)]),e("div",Se,[t[11]||(t[11]=e("label",{class:"text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"},"Show:",-1)),e("select",{onChange:q,class:"bg-transparent text-sm font-bold text-gray-700 dark:text-gray-200 focus:outline-none cursor-pointer"},[...t[10]||(t[10]=[e("option",{value:"5",selected:""},"5",-1),e("option",{value:"10"},"10",-1),e("option",{value:"25"},"25",-1)])],32)])])]),e("div",je,[_(f(C),{ref_key:"dtInstance",ref:k,data:I.jadwal,options:K,class:"w-full display stripe hover cell-border"},{"column-0":D(a=>[e("span",Te,g(a.cellData),1)]),"column-2":D(a=>[e("div",$e,[e("button",{onClick:s=>P(a.rowData),class:"p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition",title:"Edit"},[...t[12]||(t[12]=[e("i",{class:"fas fa-edit"},null,-1)])],8,Be),e("button",{onClick:s=>O(a.rowData.id),class:"p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition",title:"Hapus"},[...t[13]||(t[13]=[e("i",{class:"fas fa-trash"},null,-1)])],8,Ie)])]),default:D(()=>[t[14]||(t[14]=e("thead",null,[e("tr",{class:"text-left text-gray-500 dark:text-gray-400 border-b dark:border-gray-700"},[e("th",{class:"pb-4 font-semibold uppercase text-[12px] tracking-wider text-center"},"No"),e("th",{class:"pb-4 font-semibold uppercase text-[12px] tracking-wider text-center"},"Jadwal Pelaksanaan"),e("th",{class:"pb-4 font-semibold uppercase text-[12px] tracking-wider text-center"},"Aksi ")])],-1))]),_:1},8,["data"])]),e("div",ze,[S.value.length>0?(u(),p("div",Le," Menampilkan "+g(S.value.length)+" Data Terfilter ",1)):j("",!0),(u(!0),p(L,null,E(S.value,(a,s)=>(u(),p("div",{key:a.id,class:"bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-all active:scale-[0.98]"},[e("div",Me,[e("div",Ae,[e("div",Ee,g(s+1),1),e("div",null,[e("h4",Ne,g(a.tanggal_setoran?new Date(a.tanggal_setoran).toLocaleDateString("id-ID",{day:"numeric",month:"long",year:"numeric"}):"-"),1),t[15]||(t[15]=e("span",{class:"text-xs text-gray-500 dark:text-gray-400"},"Jadwal Pelaksanaan",-1))])]),e("span",Je,g(new Date(a.tanggal_setoran).toLocaleDateString("id-ID",{weekday:"long"})),1)]),e("div",Pe,[e("button",{onClick:m=>P(a),class:"flex items-center justify-center gap-2 py-2 bg-amber-50 dark:bg-amber-900/20 text-amber-600 rounded-xl text-[11px] font-bold"},[...t[16]||(t[16]=[e("i",{class:"fas fa-edit"},null,-1),c(" Edit ",-1)])],8,Oe),e("button",{onClick:m=>O(a.id),class:"flex items-center justify-center gap-2 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 rounded-xl text-[11px] font-bold"},[...t[17]||(t[17]=[e("i",{class:"fas fa-trash"},null,-1),c(" Hapus ",-1)])],8,Re)])]))),128)),S.value.length===0?(u(),p("div",He,[...t[18]||(t[18]=[e("i",{class:"fas fa-search text-4xl mb-3 opacity-20"},null,-1),e("p",{class:"text-sm font-medium"},"Data tidak ditemukan",-1),e("p",{class:"text-[10px]"},"Coba gunakan kata kunci pencarian lain",-1)])])):j("",!0),x.value.recordsDisplay>0?(u(),p("div",Fe,[e("span",Ve,[t[19]||(t[19]=c(" Menampilkan ",-1)),e("span",Ke,g(x.value.start+1)+"-"+g(x.value.end),1),t[20]||(t[20]=c(" dari ",-1)),e("span",Ue,g(x.value.recordsDisplay),1),t[21]||(t[21]=c(" data ",-1))]),e("div",Ge,[e("button",{onClick:U,disabled:x.value.page===0,class:"flex-1 py-3 px-4 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-700 disabled:opacity-30 disabled:grayscale bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 active:scale-95"},[...t[22]||(t[22]=[e("i",{class:"fas fa-chevron-left text-[10px]"},null,-1),c(" Sebelumnya ",-1)])],8,We),e("button",{onClick:G,disabled:x.value.page>=x.value.pages-1,class:"flex-1 py-3 px-4 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-700 disabled:opacity-30 disabled:grayscale bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 active:scale-95"},[...t[23]||(t[23]=[c(" Berikutnya ",-1),e("i",{class:"fas fa-chevron-right text-[10px]"},null,-1)])],8,qe)]),e("div",Ye,[(u(!0),p(L,null,E(x.value.pages,a=>(u(),p("div",{key:a,class:z(["w-1.5 h-1.5 rounded-full transition-all duration-300",a===x.value.page+1?"bg-emerald-500 w-4":"bg-gray-300 dark:bg-gray-700"])},null,2))),128))])])):j("",!0)])])])]),_:1},8,["sidebardata"])],64))}};export{ut as default};
