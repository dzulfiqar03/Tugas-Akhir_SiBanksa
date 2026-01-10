let isUploading = false

function setValue(fieldName, val) {
    // 1. Cari semua elemen yang punya ID tersebut
    const elements = document.querySelectorAll(`#${fieldName}, [name='${fieldName}']`);

    let target = null;

    // 2. Pilih elemen yang sedang ditampilkan (tidak tersembunyi oleh x-show)
    elements.forEach(el => {
        if (el.offsetWidth > 0 || el.offsetHeight > 0) {
            target = el;
        }
    });

    // 3. Jika tidak ada yang visible, ambil yang pertama saja
    if (!target) target = elements[0];

    if (target) {
        // Isi value
        target.value = val;

        // Pemicu agar Alpine.js/Browser sadar ada perubahan
        target.dispatchEvent(new Event('input', { bubbles: true }));
        target.dispatchEvent(new Event('change', { bubbles: true }));

        // Efek visual sukses (Highlight)
        target.classList.add('ring-4', 'ring-emerald-500/40', 'bg-emerald-100');
        setTimeout(() => {
            target.classList.remove('ring-4', 'ring-emerald-500/40', 'bg-emerald-100');
        }, 2000);

        console.log(`Berhasil Mengisi Tampilan: ${fieldName} = ${val}`);
    } else {
        console.error(`Gagal! ID '${fieldName}' tidak ditemukan di form ini.`);
    }
}


async function detectGender(file) {

    const MODEL_URL = 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights';
    await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
    await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
    await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
    await faceapi.nets.ageGenderNet.loadFromUri(MODEL_URL);

    const img = await faceapi.bufferToImage(file);


    const detection = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks()
        .withAgeAndGender();

    if (detection) {
        console.log('Gender Terdeteksi:', detection.gender);


        const genderValue = (detection.gender === 'female') ? '2' : '1';

        const radio = document.querySelector(`input[name='id_gender'][value='${genderValue}']`);
        if (radio) radio.checked = true;
    }
};

window.barcodeReader = async function (file) {

    const html5QrCode = new Html5Qrcode('barcode-engine');
    try {

        const barcodeResult = await html5QrCode.scanFile(file, {
            formatsToSupport: [
                Html5QrcodeSupportedFormats.QR_CODE,
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.CODE_39,
                Html5QrcodeSupportedFormats.EAN_13
            ]
        });

        console.log('Barcode Terdeteksi:', barcodeResult);
        if (barcodeResult) {

            if (barcodeResult.match(/^\d{16}$/)) {
                setValue('nik', barcodeResult);
            } else {

                setValue('fullName', barcodeResult);
            }
        }
    } catch (err) {
        console.warn('Barcode tidak ditemukan di gambar ini, lanjut ke OCR teks...');
    }
}


window.extractName = function(text) {
    if (!text) return null;

    // 1. Bersihkan noise karakter yang sering menempel di huruf balok
    let cleanText = text
        .replace(/[|!7;}{]/g, ':') 
        .replace(/©/g, 'O');

    const patterns = [
        // Pola 1: Cari kata "Nama", lalu ambil semua HURUF KAPITAL setelahnya 
        // sampai bertemu huruf kecil pertama (label berikutnya) atau baris baru
        /Nama\s*[:\s]+([A-Z\s'.]{3,})(?=[a-z]|$)/,

        // Pola 2: Mengambil blok huruf kapital di antara NIK dan label "Tempat"
        /\d{16}\s+([A-Z\s'.]{3,})(?=\s*[A-Z][a-z])/ ,

        // Pola 3: Menangkap baris yang isinya HANYA huruf kapital (Dokumen General)
        // Cocok untuk ijazah atau sertifikat di mana nama berdiri sendiri
        /^([A-Z\s'.]{5,})$/m,

        // Pola 4: Kasus teks menempel (NamaMUHAMMAD DZULFIOAR)
        // Mengambil huruf kapital yang didahului kata "Nama" dan diikuti label "Tempat"
        /Nama\s*([A-Z\s'.]{3,})(?=\s*Tempat|Tgl|Lahir)/i
    ];

    for (let pattern of patterns) {
        const match = cleanText.match(pattern);
        if (match && match[1]) {
            let result = match[1].trim();

            // Filter kata kunci sistem agar tidak dianggap nama
            if (!/PROVINSI|KABUPATEN|KOTA|NIK|KARTU|TANDA|INDONESIA/i.test(result)) {
                return result.toUpperCase();
            }
        }
    }

    // Strategi Terakhir: Cari baris terpanjang yang berisi huruf kapital semua
    const lines = cleanText.split('\n');
    let longestName = "";
    lines.forEach(line => {
        const cleanedLine = line.trim();
        // Cek apakah baris ini murni huruf kapital & spasi
        if (/^[A-Z\s'.]{5,}$/.test(cleanedLine)) {
            if (!/PROVINSI|KABUPATEN|KOTA|NIK|KARTU|TANDA/i.test(cleanedLine)) {
                if (cleanedLine.length > longestName.length) {
                    longestName = cleanedLine;
                }
            }
        }
    });

    return longestName || null;
};


window.extracttelNum = function (text) {
    if (!text) return null;

    const cleanText = text.replace(/[|_~;]/g, '');

    const patterns = [
        /(?:Telp|Phone|HP|WA|WhatsApp)\s*[:\s]*(\+?\d{2,4}[\s-]?\d{3,}[\s-]?\d{3,})/i,

        /(\+62[\s-]?8\d{1,2}[\s-]?\d{3,4}[\s-]?\d{3,4})/g,

        /(08\d{1,2}[\s-]?\d{3,4}[\s-]?\d{3,4})/g,

        /\b(08\d{8,11})\b/g
    ];

    for (let pattern of patterns) {
        const match = cleanText.match(pattern);
        if (match) {
            let result = (match[1] ? match[1] : match[0]).trim();

            result = result.replace(/[^\d+]/g, '');

            if (result.length >= 10) return result;
        }
    }



    return null;
};


window.OCRDoc = async function (file) {

    const { data: { text } } = await Tesseract.recognize(file, 'ind');
    console.log('Raw OCR Text:', text);

    const nikClean = text.replace(/\s/g, '').match(/\d{16}/);
    if (nikClean) setValue('nik', nikClean[0]);

    const nameMatch = window.extractName(text);
    if (nameMatch) {
    // 1. Set Nama Lengkap (Hasil dari extractName sudah Kapital)
    setValue('fullName', nameMatch);

    // 2. Generate Username yang Bersih
    // Menghapus spasi berlebih dan karakter non-huruf
    const nameParts = nameMatch.split(/\s+/).filter(part => part.length > 0);
    let baseName = nameParts[0].toLowerCase().replace(/[^a-z]/g, '');

    // Jika nama depan terlalu pendek (misal: "A. Rahman"), gabungkan dengan nama kedua
    if (baseName.length < 3 && nameParts.length > 1) {
        baseName += nameParts[1].toLowerCase().replace(/[^a-z]/g, '');
    }

        const randomNumber = Math.floor(1000 + Math.random() * 9000);

        setValue('username', `${baseName}${randomNumber}`);
    }

    const alamatMatch = text.match(/Alamat[\s.:]+([^\n]+)/i);
    if (alamatMatch) {
        setValue('address', alamatMatch[1].trim());
    }

    const rtMatch = text.match(/RT[^\d]+(\d{3})/i);
    if (rtMatch) {
        const cleanRT = parseInt(rtMatch[1]).toString();
        setValue('rt', cleanRT);
    }

    const telNumMatch = window.extracttelNum(text)
    if (telNumMatch) {
        setValue('phoneNumber', telNumMatch);
    }
}


window.handleScanDoc = async function (event) {
    const file = event.target.files[0];
    if (!file) return;
    isUploading = true;

    try {

        await window.barcodeReader(file);

        await window.OCRDoc(file);

        await window.detectGender(file);

        alert('Data KTP berhasil diekstrak!');
    } catch (e) {
        alert('Gagal membaca gambar.');
    } finally {
        isUploading = false;
    }
}