import { connectDaoWallet, getWalletOptions, getConnectedAddress } from "./dao-wallet.js";

export function populateWalletOptions(selectEl) {
  const options = getWalletOptions();
  Object.entries(options).forEach(([key, label]) => {
    const option = document.createElement("option");
    option.value = key;
    option.textContent = label;
    selectEl.appendChild(option);
  });
}

export async function handleWalletConnect(selectEl, statusEl) {
  const walletKey = selectEl.value;
  if (!walletKey) return;

  try {
    statusEl.textContent = "Connecting...";
    const address = await connectDaoWallet(walletKey);
    statusEl.textContent = address;
  } catch (error) {
    statusEl.textContent = error.message;
  }
}

export async function hydrateWalletStatus(statusEl) {
  const address = await getConnectedAddress();
  if (address) {
    statusEl.textContent = address;
  }
}
