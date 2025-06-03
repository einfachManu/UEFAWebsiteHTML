// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyCkkZcBLN_4h4VWrqNXBtc8qjR1wkJTrs4",
  authDomain: "umemfma.firebaseapp.com",
  projectId: "umemfma",
  storageBucket: "umemfma.firebasestorage.app",
  messagingSenderId: "283060099490",
  appId: "1:283060099490:web:ef4dd094861ec8a6a37afb",
  measurementId: "G-N071ZLE4S8"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);