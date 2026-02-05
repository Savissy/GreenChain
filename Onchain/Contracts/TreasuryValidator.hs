{-# LANGUAGE DataKinds #-}
{-# LANGUAGE NoImplicitPrelude #-}
{-# LANGUAGE ScopedTypeVariables #-}
{-# LANGUAGE TemplateHaskell #-}
{-# LANGUAGE MultiParamTypeClasses #-}

module TreasuryValidator where

import PlutusTx
import PlutusTx.Prelude
import Plutus.V2.Ledger.Api
import Plutus.V2.Ledger.Contexts

{-
  Treasury contract
  - verify proposal approval/quorum
  - controlled fund release
  - DAO/admin multi-sig gate
-}

newtype ProposalId = ProposalId Integer
PlutusTx.unstableMakeIsData ''ProposalId


data TreasuryDatum = TreasuryDatum
  { treasuryProposalId :: ProposalId
  , quorumMet :: Bool
  , approvalHash :: BuiltinByteString
  }

PlutusTx.unstableMakeIsData ''TreasuryDatum

{-# INLINABLE mkTreasuryValidator #-}
mkTreasuryValidator :: [PubKeyHash] -> TreasuryDatum -> () -> ScriptContext -> Bool
mkTreasuryValidator daoSigners datum _ ctx =
    traceIfFalse "quorum not met" (quorumMet datum)
    && traceIfFalse "DAO signatures missing" signedByDao
  where
    info :: TxInfo
    info = scriptContextTxInfo ctx

    signedByDao :: Bool
    signedByDao =
      let required = length daoSigners
          signed = length (filter (txSignedBy info) daoSigners)
      in signed >= required

validator :: [PubKeyHash] -> Validator
validator daoSigners = mkValidatorScript
  $$(PlutusTx.compile [|| \signers -> mkTreasuryValidator signers ||])
    `PlutusTx.applyCode` PlutusTx.liftCode daoSigners
