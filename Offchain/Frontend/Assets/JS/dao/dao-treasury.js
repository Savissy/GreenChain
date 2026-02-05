import { fetchTreasuryQueue, executeTreasury, mintMember, burnMember } from "./dao-api.js";
import { formatDateTime } from "./dao-utils.js";
import { populateWalletOptions, handleWalletConnect, hydrateWalletStatus } from "./dao-ui.js";
import { getConnectedAddress } from "./dao-wallet.js";

const walletSelect = document.getElementById("walletSelect");
const walletStatus = document.getElementById("walletStatus");
const connectBtn = document.getElementById("connectWallet");
const treasuryList = document.getElementById("treasuryList");
const mintForm = document.getElementById("mintForm");
const burnForm = document.getElementById("burnForm");

populateWalletOptions(walletSelect);
connectBtn.addEventListener("click", () => handleWalletConnect(walletSelect, walletStatus));

hydrateWalletStatus(walletStatus);

async function loadTreasuryQueue() {
  const data = await fetchTreasuryQueue();
  treasuryList.innerHTML = "";

  if (!data.approved || data.approved.length === 0) {
    treasuryList.innerHTML = "<p>No approved proposals ready for execution.</p>";
    return;
  }

  data.approved.forEach((proposal) => {
    const card = document.createElement("div");
    card.className = "dao-card";
    card.innerHTML = `
      <h3>${proposal.title}</h3>
      <p>${proposal.summary || "No summary provided."}</p>
      <div class="dao-meta">
        <span>Yes: ${proposal.yes_weight}</span>
        <span>Quorum: ${proposal.quorum}</span>
        <span>Closed: ${formatDateTime(proposal.created_at)}</span>
      </div>
      <div class="dao-actions">
        <input type="text" placeholder="Execution action" data-action-id="${proposal.id}">
        <input type="text" placeholder="Tx hash (optional)" data-hash-id="${proposal.id}">
        <button data-exec-id="${proposal.id}">Execute</button>
      </div>
    `;
    treasuryList.appendChild(card);
  });

  treasuryList.querySelectorAll("button").forEach((button) => {
    button.addEventListener("click", () => executeProposal(button.dataset.execId));
  });
}

async function executeProposal(proposalId) {
  const address = await getConnectedAddress();
  if (!address) {
    alert("Connect a wallet first.");
    return;
  }

  const actionInput = treasuryList.querySelector(`input[data-action-id="${proposalId}"]`);
  const hashInput = treasuryList.querySelector(`input[data-hash-id="${proposalId}"]`);

  const action = actionInput?.value.trim();
  const txHash = hashInput?.value.trim();

  if (!action) {
    alert("Provide an execution action.");
    return;
  }

  const response = await executeTreasury({
    proposal_id: Number(proposalId),
    executor_wallet: address,
    action,
    tx_hash: txHash
  });

  if (response.success) {
    await loadTreasuryQueue();
  } else {
    alert(response.error || "Execution failed");
  }
}

mintForm.addEventListener("submit", async (event) => {
  event.preventDefault();
  const wallet = mintForm.querySelector("#mintWallet").value.trim();
  const policyId = mintForm.querySelector("#mintPolicy").value.trim();
  const assetName = mintForm.querySelector("#mintAsset").value.trim();
  const weight = Number(mintForm.querySelector("#mintWeight").value) || 1;

  const response = await mintMember({
    wallet_address: wallet,
    nft_policy_id: policyId,
    nft_asset_name: assetName,
    weight
  });

  if (response.success) {
    mintForm.reset();
    alert("Member minted.");
  } else {
    alert(response.error || "Mint failed");
  }
});

burnForm.addEventListener("submit", async (event) => {
  event.preventDefault();
  const wallet = burnForm.querySelector("#burnWallet").value.trim();

  const response = await burnMember({ wallet_address: wallet });
  if (response.success) {
    burnForm.reset();
    alert("Member burned.");
  } else {
    alert(response.error || "Burn failed");
  }
});

loadTreasuryQueue();
