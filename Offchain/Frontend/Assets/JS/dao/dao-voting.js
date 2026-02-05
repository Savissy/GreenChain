import { fetchProposals, submitVote, getMembership } from "./dao-api.js";
import { formatDateTime } from "./dao-utils.js";
import { populateWalletOptions, handleWalletConnect, hydrateWalletStatus } from "./dao-ui.js";
import { getConnectedAddress, signPayload } from "./dao-wallet.js";

const walletSelect = document.getElementById("walletSelect");
const walletStatus = document.getElementById("walletStatus");
const connectBtn = document.getElementById("connectWallet");
const voteList = document.getElementById("voteList");
const membershipStatus = document.getElementById("membershipStatus");

populateWalletOptions(walletSelect);
connectBtn.addEventListener("click", () => handleWalletConnect(walletSelect, walletStatus));

hydrateWalletStatus(walletStatus);

async function refreshMembership() {
  const address = await getConnectedAddress();
  if (!address) {
    membershipStatus.textContent = "Connect a wallet to check NFT membership.";
    return null;
  }

  const membership = await getMembership(address);
  if (!membership.is_member) {
    membershipStatus.textContent = "Not a governance NFT holder.";
    return null;
  }

  membershipStatus.textContent = `Member weight: ${membership.member.weight}`;
  return membership.member;
}

async function loadVotes() {
  await refreshMembership();

  const data = await fetchProposals("active");
  voteList.innerHTML = "";

  if (!data.proposals || data.proposals.length === 0) {
    voteList.innerHTML = "<p>No active proposals.</p>";
    return;
  }

  data.proposals.forEach((proposal) => {
    const card = document.createElement("div");
    card.className = "dao-card";
    card.innerHTML = `
      <h3>${proposal.title}</h3>
      <p>${proposal.summary || "No summary provided."}</p>
      <div class="dao-meta">
        <span>Deadline: ${proposal.deadline ? formatDateTime(proposal.deadline) : "None"}</span>
        <span>Yes: ${proposal.yes_weight} | No: ${proposal.no_weight}</span>
      </div>
      <div class="dao-actions">
        <button data-choice="yes" data-id="${proposal.id}">Vote Yes</button>
        <button data-choice="no" data-id="${proposal.id}">Vote No</button>
        <button data-choice="abstain" data-id="${proposal.id}">Abstain</button>
      </div>
    `;
    voteList.appendChild(card);
  });

  voteList.querySelectorAll("button").forEach((btn) => {
    btn.addEventListener("click", () => submitVoteForProposal(btn.dataset.id, btn.dataset.choice));
  });
}

async function submitVoteForProposal(proposalId, choice) {
  const address = await getConnectedAddress();
  if (!address) {
    alert("Connect a wallet first.");
    return;
  }

  const membership = await getMembership(address);
  if (!membership.is_member) {
    alert("Only governance NFT holders can vote.");
    return;
  }

  const payload = {
    proposal_id: Number(proposalId),
    choice,
    wallet_address: address,
    timestamp: Date.now()
  };

  const signed = await signPayload(address, payload);

  const response = await submitVote({
    proposal_id: payload.proposal_id,
    wallet_address: address,
    choice,
    message: signed.message,
    signature: signed.signature
  });

  if (response.success) {
    await loadVotes();
  } else {
    alert(response.error || "Vote failed");
  }
}

loadVotes();
