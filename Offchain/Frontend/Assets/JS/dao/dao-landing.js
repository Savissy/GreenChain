import { populateWalletOptions, handleWalletConnect, hydrateWalletStatus } from "./dao-ui.js";

const walletSelect = document.getElementById("walletSelect");
const walletStatus = document.getElementById("walletStatus");
const connectBtn = document.getElementById("connectWallet");

populateWalletOptions(walletSelect);
connectBtn.addEventListener("click", () => handleWalletConnect(walletSelect, walletStatus));

hydrateWalletStatus(walletStatus);
