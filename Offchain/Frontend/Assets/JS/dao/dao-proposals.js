import { createProposal, fetchProposals } from "./dao-api.js";
import { hashProposalContent, formatDateTime } from "./dao-utils.js";
import { populateWalletOptions, handleWalletConnect, hydrateWalletStatus } from "./dao-ui.js";
import { getConnectedAddress } from "./dao-wallet.js";

const form = document.getElementById("proposalForm");
const proposalList = document.getElementById("proposalList");
const walletSelect = document.getElementById("walletSelect");
const walletStatus = document.getElementById("walletStatus");
const connectBtn = document.getElementById("connectWallet");

populateWalletOptions(walletSelect);

connectBtn.addEventListener("click", () => handleWalletConnect(walletSelect, walletStatus));

hydrateWalletStatus(walletStatus);

form.addEventListener("submit", async (event) => {
  event.preventDefault();

  const address = await getConnectedAddress();
  if (!address) {
    alert("Connect a wallet first.");
    return;
  }

  const title = form.querySelector("#proposalTitle").value.trim();
  const summary = form.querySelector("#proposalSummary").value.trim();
  const details = form.querySelector("#proposalDetails").value.trim();
  const deadline = form.querySelector("#proposalDeadline").value;
  const quorum = Number(form.querySelector("#proposalQuorum").value) || 1;

  if (!title || !details) {
    alert("Add a title and details.");
    return;
  }

  const hash = await hashProposalContent(details);

  const response = await createProposal({
    title,
    summary,
    proposal_hash: hash,
    proposer_wallet: address,
    deadline: deadline || null,
    quorum,
    activate: true
  });

  if (response.success) {
    form.reset();
    await loadProposals();
  } else {
    alert(response.error || "Failed to create proposal.");
  }
});

async function loadProposals() {
  const data = await fetchProposals();
  proposalList.innerHTML = "";

  if (!data.proposals || data.proposals.length === 0) {
    proposalList.innerHTML = "<p>No proposals yet.</p>";
    return;
  }

  data.proposals.forEach((proposal) => {
    const card = document.createElement("div");
    card.className = "dao-card";
    card.innerHTML = `
      <h3>${proposal.title}</h3>
      <p>${proposal.summary || "No summary provided."}</p>
      <div class="dao-meta">
        <span>Status: ${proposal.status}</span>
        <span>Created: ${formatDateTime(proposal.created_at)}</span>
        <span>Deadline: ${proposal.deadline ? formatDateTime(proposal.deadline) : "None"}</span>
      </div>
      <div class="dao-hash">Hash: ${proposal.proposal_hash}</div>
    `;
    proposalList.appendChild(card);
  });
}

loadProposals();
