{-# LANGUAGE DataKinds #-}
{-# LANGUAGE NoImplicitPrelude #-}
{-# LANGUAGE ScopedTypeVariables #-}
{-# LANGUAGE TemplateHaskell #-}
{-# LANGUAGE MultiParamTypeClasses #-}

module VotingValidator where

import PlutusTx
import PlutusTx.Prelude
import Plutus.V2.Ledger.Api
import Plutus.V2.Ledger.Contexts

{-
  Voting contract
  - NFT-gated voting
  - one vote per wallet
  - weighted tally
  - replay protection
-}

newtype ProposalId = ProposalId Integer
PlutusTx.unstableMakeIsData ''ProposalId


data VoteChoice = VoteYes | VoteNo | VoteAbstain
PlutusTx.unstableMakeIsData ''VoteChoice


data VoteDatum = VoteDatum
  { voteProposalId :: ProposalId
  , voter :: PubKeyHash
  , voteChoice :: VoteChoice
  , weight :: Integer
  , voteNonce :: BuiltinByteString
  }

PlutusTx.unstableMakeIsData ''VoteDatum

{-# INLINABLE mkVotingValidator #-}
mkVotingValidator :: CurrencySymbol -> TokenName -> VoteDatum -> () -> ScriptContext -> Bool
mkVotingValidator nftPolicy nftName datum _ ctx =
    traceIfFalse "governance NFT missing" hasGovernanceNft
    && traceIfFalse "wallet not signed" signedByVoter
    && traceIfFalse "invalid weight" (weight datum > 0)
  where
    info :: TxInfo
    info = scriptContextTxInfo ctx

    signedByVoter :: Bool
    signedByVoter = txSignedBy info (voter datum)

    hasGovernanceNft :: Bool
    hasGovernanceNft =
      let value = valuePaidTo info (voter datum)
      in valueOf value nftPolicy nftName >= 1

validator :: CurrencySymbol -> TokenName -> Validator
validator nftPolicy nftName = mkValidatorScript
  $$(PlutusTx.compile [|| \policy name -> mkVotingValidator policy name ||])
    `PlutusTx.applyCode` PlutusTx.liftCode nftPolicy
    `PlutusTx.applyCode` PlutusTx.liftCode nftName
