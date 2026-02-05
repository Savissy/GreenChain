import { Lucid, Blockfrost } from "https://unpkg.com/lucid-cardano/web/mod.js";

const walletOptions = {
  nami: "Nami",
  eternl: "Eternl",
  flint: "Flint",
  lace: "Lace"
};

let lucid = null;
let connectedWallet = null;

export function getWalletOptions() {
  return walletOptions;
}

export async function connectDaoWallet(walletKey) {
  const cardano = window.cardano || {};
  const provider = cardano[walletKey];

  if (!provider) {
    throw new Error(`${walletOptions[walletKey] || "Wallet"} not installed`);
  }

  const api = await provider.enable();

  lucid = await Lucid.new(
    new Blockfrost(
      "https://cardano-preprod.blockfrost.io/api/v0",
      "preprodsJw0qxJfYA3iXqc7HRc0rtyI8Dmv9ny3"
    ),
    "Preprod"
  );

  lucid.selectWallet(api);
  connectedWallet = walletKey;
  window.lucid = lucid;

  return await lucid.wallet.address();
}

export function getLucid() {
  return lucid;
}

export function getConnectedWalletKey() {
  return connectedWallet;
}

export async function getConnectedAddress() {
  if (!lucid) return null;
  return await lucid.wallet.address();
}

export async function signPayload(address, payload) {
  if (!lucid) throw new Error("Wallet not connected");

  const message = JSON.stringify(payload);
  const signature = await lucid.wallet.signMessage(
    address,
    new TextEncoder().encode(message)
  );

  return { message, signature };
}
